<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResultFormRequest;
use App\Models\Consumable;
use App\Models\Equipment;
use App\Models\ExamRequestItem;
use App\Models\ResultLabo;
use App\Models\ResultLaboDetail;
use App\Models\StockMovement;
use App\Services\ExamRequestService;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LaboResultController extends Controller
{
    /**
     * Show the result entry form for a given exam request item.
     *
     * @return View|RedirectResponse
     */
    public function create(ExamRequestItem $item)
    {
        // only the owning laboratory may enter results
        $lab = auth()->user()->staff->laboratory;
        if ($item->examRequest->labo_id !== $lab->id) {
            abort(403);
        }

        // block editing once the doctor has validated the results
        if ($item->examRequest->approved_by_doctor) {
            return redirect()
                ->route('center.exam-requests')
                ->with('error', 'Impossible de modifier — le médecin a déjà validé et interprété les résultats de cette demande.');
        }

        // eager load the exam, request and existing result relations
        $item->load([
            'exam.parameters',
            'exam.examConsumables.consumable',
            'exam.examEquipment.equipment',
            'examRequest.patient.user',
            'examRequest.doctor.user',
            'resultLabo.details',
        ]);

        // available lab consumables and equipment for the form
        $consumables = $lab->consumables()->where('is_archive', false)->get();
        $equipment = $lab->equipment()->where('is_archive', false)->get();

        // Batch-load consumables once
        $labConsumablesByName = $lab->consumables()
            ->where('is_archive', false)
            ->get()
            ->keyBy('name');

        // prefill consumables suggested by the exam definition
        $preloadedConsumables = [];
        foreach ($item->exam->examConsumables as $ec) {
            if ($ec->consumable && isset($labConsumablesByName[$ec->consumable->name])) {
                $lc = $labConsumablesByName[$ec->consumable->name];
                $preloadedConsumables[] = [
                    'id' => (string) $lc->id,
                    'name' => $lc->name,
                    'unit' => $lc->unit,
                    'stock' => $lc->quantity,
                    'quantity' => min($ec->quantity_needed, $lc->quantity),
                    'isSuggested' => true,
                ];
            }
        }

        return view(
            'center.results.create',
            compact('item', 'consumables', 'equipment', 'preloadedConsumables')
        );
    }

    /**
     * Save or update the result for an exam request item.
     *
     * @return RedirectResponse
     */
    public function store(StoreResultFormRequest $request, ExamRequestItem $item)
    {
        // only the owning laboratory may save results
        $lab = auth()->user()->staff;
        if ($item->examRequest->labo_id !== $lab->laboratory_id) {
            abort(403);
        }

        $lab = auth()->user()->staff;
        if ($item->examRequest->labo_id !== $lab->laboratory_id) {
            abort(403);
        }

        if ($item->examRequest->approved_by_doctor) {
            return redirect()
                ->route('center.exam-requests')
                ->with('error', 'Impossible de modifier — le médecin a déjà validé et interprété les résultats de cette demande.');
        }

        $result = null;

        DB::transaction(function () use ($request, $item, $lab, &$result) {
            // Check if result already exists
            $result = ResultLabo::where(
                'exam_request_item_id',
                $item->id
            )->first();

            // If exists => update
            if ($result) {
                $result->update([
                    'interpretation' => $request->interpretation,
                ]);

                // 1. Revert previous stock movements & quantities
                foreach ($result->consumables as $oldConsumable) {
                    $qtyUsed = $oldConsumable->pivot->quantity_used;
                    StockService::add($oldConsumable, $qtyUsed, "Annulation / Mise à jour du résultat d'examen #".$result->id);
                }

                // Remove old details, consumables and equipment
                $result->details()->delete();
                $result->consumables()->detach();
                $result->equipment()->detach();
            } else {
                // Create new result
                $result = ResultLabo::create([
                    'exam_request_item_id' => $item->id,
                    'staff_id' => $lab->id,
                    'interpretation' => $request->interpretation,
                    'is_archive' => false,
                ]);
            }

            // Save parameters
            foreach ($request->parameters as $parameter) {
                ResultLaboDetail::create([
                    'result_labo_id' => $result->id,
                    'parameter' => $parameter['name'],
                    'value' => $parameter['value'],
                    'status' => $parameter['status'],
                    'reference_range' => $parameter['range'],
                    'unit' => $parameter['unit'] ?? null,
                    'is_archive' => false,
                ]);
            }

            // Save consumables & update stock
            if ($request->has('consumables')) {
                foreach ($request->consumables as $cData) {
                    $consumableId = $cData['id'];
                    $quantityUsed = (int) $cData['quantity'];

                    $consumable = Consumable::findOrFail($consumableId);
                    if ($consumable->labo_id !== $lab->laboratory_id) {
                        abort(403);
                    }
                    StockService::deduct($consumable, $quantityUsed, "Utilisé pour le résultat d'examen #".$result->id);

                    // Attach to result
                    $result->consumables()->attach($consumableId, [
                        'quantity_used' => $quantityUsed,
                        'is_archive' => false,
                    ]);
                }
            }

            // Save equipment used
            if ($request->has('equipment')) {
                foreach ($request->equipment as $equipId) {
                    $equipment = Equipment::findOrFail($equipId);
                    if ($equipment->labo_id !== $lab->laboratory_id) {
                        abort(403);
                    }
                    $result->equipment()->attach($equipId, [
                        'is_archive' => false,
                    ]);
                }
            }
        });

        // Check if all items in this request have results using ExamRequestService
        ExamRequestService::checkCompletion($item->examRequest);

        // redirect back to the exam requests list
        return redirect()
            ->route('center.exam-requests')
            ->with('success', 'Résultat enregistré avec succès.');
    }

    /**
     * Show the form to edit an existing result.
     *
     * @return View|RedirectResponse
     */
    public function edit(ResultLabo $result)
    {
        // only the owning laboratory may edit the result
        $lab = auth()->user()->staff->laboratory;
        if ($result->examRequestItem->examRequest->labo_id !== $lab->id) {
            abort(403);
        }

        if ($result->examRequestItem->examRequest->approved_by_doctor) {
            return redirect()
                ->route('center.exam-requests')
                ->with('error', 'Impossible de modifier — le médecin a déjà validé et interprété les résultats de cette demande.');
        }

        // eager load result details, consumables, equipment and request context
        $result->load([
            'details',
            'consumables',
            'equipment',
            'examRequestItem.exam.parameters',
            'examRequestItem.exam.examConsumables.consumable',
            'examRequestItem.exam.examEquipment.equipment',
            'examRequestItem.examRequest.patient.user',
            'examRequestItem.examRequest.doctor.user',
        ]);

        // consumables and equipment available in the laboratory
        $consumables = $lab->consumables()->where('is_archive', false)->get();
        $equipment = $lab->equipment()->where('is_archive', false)->get();

        // Batch-load suggested consumable names
        $suggestedNames = $result->examRequestItem->exam->examConsumables()
            ->with('consumable')
            ->get()
            ->pluck('consumable.name')
            ->filter()
            ->toArray();
        $suggestedNames = array_flip($suggestedNames);

        $preloadedConsumables = [];
        foreach ($result->consumables as $c) {
            $preloadedConsumables[] = [
                'id' => (string) $c->id,
                'name' => $c->name,
                'unit' => $c->unit,
                'stock' => $c->quantity + $c->pivot->quantity_used,
                'quantity' => $c->pivot->quantity_used,
                'isSuggested' => isset($suggestedNames[$c->name]),
            ];
        }

        return view(
            'center.results.edit',
            compact('result', 'consumables', 'equipment', 'preloadedConsumables')
        );
    }

    /**
     * Update an existing result with new parameters, consumables and equipment.
     *
     * @return RedirectResponse
     */
    public function update(Request $request, ResultLabo $result)
    {
        // only the owning laboratory may update the result
        $lab = auth()->user()->staff->laboratory;
        if ($result->examRequestItem->examRequest->labo_id !== $lab->id) {
            abort(403);
        }

        // block editing once the doctor has validated the results
        if ($result->examRequestItem->examRequest->approved_by_doctor) {
            return redirect()
                ->route('center.exam-requests')
                ->with('error', 'Impossible de modifier — le médecin a déjà validé et interprété les résultats de cette demande.');
        }
        // validate the submitted parameters, consumables and equipment
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

        DB::transaction(function () use ($request, $result, $lab) {
            $result->update([
                'interpretation' => $request->interpretation,
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
                    'reason' => "Mise à jour du résultat d'examen #".$result->id.' (Restitution)',
                ]);
            }

            // 2. Remove old details, consumables and equipment associations
            $result->details()->delete();
            $result->consumables()->detach();
            $result->equipment()->detach();

            // 3. Re-save parameters
            foreach ($request->parameters as $parameter) {
                ResultLaboDetail::create([
                    'result_labo_id' => $result->id,
                    'parameter' => $parameter['name'],
                    'value' => $parameter['value'],
                    'status' => $parameter['status'],
                    'reference_range' => $parameter['range'],
                    'unit' => $parameter['unit'] ?? null,
                    'is_archive' => false,
                ]);
            }

            // 4. Save new consumables & update stock
            if ($request->has('consumables')) {
                foreach ($request->consumables as $cData) {
                    $consumableId = $cData['id'];
                    $quantityUsed = (int) $cData['quantity'];

                    $consumable = Consumable::findOrFail($consumableId);
                    if ($consumable->labo_id !== $lab->id) {
                        abort(403);
                    }
                    // Deduct stock
                    $consumable->quantity = max(0, $consumable->quantity - $quantityUsed);
                    $consumable->save();

                    // Create stock movement
                    StockMovement::create([
                        'consumable_id' => $consumable->id,
                        'quantity_change' => $quantityUsed,
                        'type' => 'out',
                        'reason' => "Utilisé pour le résultat d'examen #".$result->id,
                    ]);

                    // Attach to result
                    $result->consumables()->attach($consumableId, [
                        'quantity_used' => $quantityUsed,
                        'is_archive' => false,
                    ]);
                }
            }

            // 5. Save new equipment
            if ($request->has('equipment')) {
                foreach ($request->equipment as $equipId) {
                    $equipment = Equipment::findOrFail($equipId);
                    if ($equipment->labo_id !== $lab->id) {
                        abort(403);
                    }
                    $result->equipment()->attach($equipId, [
                        'is_archive' => false,
                    ]);
                }
            }
        });

        ExamRequestService::checkCompletion($result->examRequestItem->examRequest);

        // redirect back to the exam requests list
        return redirect()
            ->route('center.exam-requests')
            ->with('success', 'Résultat modifié avec succès.');
    }
}
