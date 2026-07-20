<?php

namespace App\Http\Controllers;

use App\Models\ExamRequestItem;
use App\Models\ResultLabo;
use App\Models\ResultLaboDetail;
use App\Models\Consumable;
use App\Models\StockMovement;
use App\Services\ExamRequestService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaboResultController extends Controller
{

    public function create(ExamRequestItem $item)
    {
        $item->load([
            'exam.parameters',
            'exam.examConsumables.consumable',
            'exam.examEquipment.equipment',
            'examRequest.patient.user',
            'resultLabo.details'
        ]);

        $lab = auth()->user()->staff->laboratory;
        $consumables = $lab->consumables()->where('is_archive', false)->get();
        $equipment = $lab->equipment()->where('is_archive', false)->get();

        // Prepare suggestion data for javascript (resolve by name in the current laboratory)
        $preloadedConsumables = [];
        foreach ($item->exam->examConsumables as $ec) {
            if ($ec->consumable) {
                $labConsumable = $lab->consumables()
                    ->where('name', $ec->consumable->name)
                    ->where('is_archive', false)
                    ->first();

                if ($labConsumable) {
                    $preloadedConsumables[] = [
                        'id' => (string)$labConsumable->id,
                        'name' => $labConsumable->name,
                        'unit' => $labConsumable->unit,
                        'stock' => $labConsumable->quantity,
                        'quantity' => min($ec->quantity_needed, $labConsumable->quantity),
                        'isSuggested' => true
                    ];
                }
            }
        }

        return view(
            'center.results.create',
            compact('item', 'consumables', 'equipment', 'preloadedConsumables')
        );
    }



    public function store(\App\Http\Requests\StoreResultFormRequest $request, ExamRequestItem $item)
    {
        $lab = auth()->user()->staff;
        $result = null;

        DB::transaction(function () use ($request, $item, $lab, &$result) {
            // Check if result already exists
            $result = ResultLabo::where(
                'exam_request_item_id',
                $item->id
            )->first();

            // If exists => update
            if($result){
                $result->update([
                    'interpretation'=>$request->interpretation,
                ]);

                // 1. Revert previous stock movements & quantities
                foreach ($result->consumables as $oldConsumable) {
                    $qtyUsed = $oldConsumable->pivot->quantity_used;
                    StockService::add($oldConsumable, $qtyUsed, "Annulation / Mise à jour du résultat d'examen #" . $result->id);
                }

                // Remove old details, consumables and equipment
                $result->details()->delete();
                $result->consumables()->detach();
                $result->equipment()->detach();
            }
            else {
                // Create new result
                $result = ResultLabo::create([
                    'exam_request_item_id'=>$item->id,
                    'staff_id'=>$lab->id,
                    'interpretation'=>$request->interpretation,
                    'is_archive'=>false
                ]);
            }

            // Save parameters
            foreach($request->parameters as $parameter)
            {
                ResultLaboDetail::create([
                    'result_labo_id'=>$result->id,
                    'parameter'=>$parameter['name'],
                    'value'=>$parameter['value'],
                    'status'=>$parameter['status'],
                    'reference_range'=>$parameter['range'],
                    'unit'=>$parameter['unit'] ?? null,
                    'is_archive'=>false
                ]);
            }

            // Save consumables & update stock
            if ($request->has('consumables')) {
                foreach ($request->consumables as $cData) {
                    $consumableId = $cData['id'];
                    $quantityUsed = (int)$cData['quantity'];

                    $consumable = Consumable::findOrFail($consumableId);
                    StockService::deduct($consumable, $quantityUsed, "Utilisé pour le résultat d'examen #" . $result->id);

                    // Attach to result
                    $result->consumables()->attach($consumableId, [
                        'quantity_used' => $quantityUsed,
                        'is_archive' => false
                    ]);
                }
            }

            // Save equipment used
            if ($request->has('equipment')) {
                foreach ($request->equipment as $equipId) {
                    $result->equipment()->attach($equipId, [
                        'is_archive' => false
                    ]);
                }
            }
        });

        // Check if all items in this request have results using ExamRequestService
        ExamRequestService::checkCompletion($item->examRequest);

        return redirect()
            ->route('center.exam-requests')
            ->with('success','Résultat enregistré avec succès.');
    }

    public function edit(ResultLabo $result)
    {
        $result->load([
            'details',
            'consumables',
            'equipment',
            'examRequestItem.exam.parameters',
            'examRequestItem.exam.examConsumables.consumable',
            'examRequestItem.exam.examEquipment.equipment',
            'examRequestItem.examRequest.patient.user'
        ]);

        $lab = auth()->user()->staff->laboratory;
        $consumables = $lab->consumables()->where('is_archive', false)->get();
        $equipment = $lab->equipment()->where('is_archive', false)->get();

        // Prepare suggestion/preselected data for javascript (resolve suggested status by name)
        $preloadedConsumables = [];
        foreach ($result->consumables as $c) {
            $isSuggested = $result->examRequestItem->exam->examConsumables()
                ->whereHas('consumable', function($query) use ($c) {
                    $query->where('name', $c->name);
                })->exists();

            $preloadedConsumables[] = [
                'id' => (string)$c->id,
                'name' => $c->name,
                'unit' => $c->unit,
                'stock' => $c->quantity + $c->pivot->quantity_used, // add back for edit limits
                'quantity' => $c->pivot->quantity_used,
                'isSuggested' => $isSuggested
            ];
        }

        return view(
            'center.results.edit',
            compact('result', 'consumables', 'equipment', 'preloadedConsumables')
        );
    }

    public function update(Request $request, ResultLabo $result)
    {
        $request->validate([
            'interpretation' => 'nullable|string',
            'parameters' => 'required|array',
            'parameters.*.name' => 'required|string',
            'parameters.*.value' => 'required|string',
            'parameters.*.status' => 'required|in:normal,high,low',
            'parameters.*.range' => 'nullable|string',
            'consumables' => 'nullable|array',
            'consumables.*.id' => 'required|exists:consumables,id',
            'consumables.*.quantity' => 'required|integer|min:1',
            'equipment' => 'nullable|array',
            'equipment.*' => 'required|exists:equipment,id',
        ]);

        DB::transaction(function () use ($request, $result) {
            $result->update([
                'interpretation'=>$request->interpretation
            ]);

            // 1. Revert previous stock movements & quantities for this result
            foreach ($result->consumables as $oldConsumable) {
                $qtyUsed = $oldConsumable->pivot->quantity_used;
                $oldConsumable->quantity += $qtyUsed;
                $oldConsumable->save();

                StockMovement::create([
                    'consumable_id' => $oldConsumable->id,
                    'quantity_change' => $qtyUsed,
                    'type' => 'in',
                    'reason' => "Mise à jour du résultat d'examen #" . $result->id . " (Restitution)",
                ]);
            }

            // 2. Remove old details, consumables and equipment associations
            $result->details()->delete();
            $result->consumables()->detach();
            $result->equipment()->detach();

            // 3. Re-save parameters
            foreach($request->parameters as $parameter)
            {
                ResultLaboDetail::create([
                    'result_labo_id'=>$result->id,
                    'parameter'=>$parameter['name'],
                    'value'=>$parameter['value'],
                    'status'=>$parameter['status'],
                    'reference_range'=>$parameter['range'],
                    'unit'=>$parameter['unit'] ?? null,
                    'is_archive'=>false
                ]);
            }

            // 4. Save new consumables & update stock
            if ($request->has('consumables')) {
                foreach ($request->consumables as $cData) {
                    $consumableId = $cData['id'];
                    $quantityUsed = (int)$cData['quantity'];

                    $consumable = Consumable::findOrFail($consumableId);
                    // Deduct stock
                    $consumable->quantity = max(0, $consumable->quantity - $quantityUsed);
                    $consumable->save();

                    // Create stock movement
                    StockMovement::create([
                        'consumable_id' => $consumable->id,
                        'quantity_change' => $quantityUsed,
                        'type' => 'out',
                        'reason' => "Utilisé pour le résultat d'examen #" . $result->id,
                    ]);

                    // Attach to result
                    $result->consumables()->attach($consumableId, [
                        'quantity_used' => $quantityUsed,
                        'is_archive' => false
                    ]);
                }
            }

            // 5. Save new equipment
            if ($request->has('equipment')) {
                foreach ($request->equipment as $equipId) {
                    $result->equipment()->attach($equipId, [
                        'is_archive' => false
                    ]);
                }
            }
        });

        ExamRequestService::checkCompletion($result->examRequestItem->examRequest);

        return redirect()
            ->route('center.exam-requests')
            ->with('success','Résultat modifié avec succès.');
    }

}