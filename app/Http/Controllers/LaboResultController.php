<?php

namespace App\Http\Controllers;

use App\Models\ExamRequestItem;
use App\Models\ResultLabo;
use App\Models\ResultLaboDetail;
use Illuminate\Http\Request;

class LaboResultController extends Controller
{

    public function create(ExamRequestItem $item)
    {
        $item->load([
            'exam.parameters',
            'examRequest.patient.user',
            'resultLabo.details'
        ]);

        return view(
            'center.results.create',
            compact('item')
        );
    }



    public function store(Request $request, ExamRequestItem $item)
    {

        $lab = auth()->user()->staff;


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


            // Remove old details
            $result->details()->delete();


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

                'is_archive'=>false

            ]);

        }




        // Check if all items in this request have results
        $examRequest = $item->examRequest;
        $allItemsCompleted = true;
        foreach ($examRequest->items as $reqItem) {
            if ($reqItem->id !== $item->id && !$reqItem->resultLabo()->exists()) {
                $allItemsCompleted = false;
                break;
            }
        }

        if ($allItemsCompleted) {
            $examRequest->update([
                'status' => 'completed'
            ]);
        }



        return redirect()

            ->route('center.exam-requests')

            ->with('success','Résultat enregistré avec succès.');

    }
public function edit(ResultLabo $result)
{
    $result->load([
        'details',
        'examRequestItem.exam.parameters',
        'examRequestItem.examRequest.patient.user'
    ]);


    return view(
        'center.results.edit',
        compact('result')
    );
}
public function update(Request $request, ResultLabo $result)
{

    $result->update([

        'interpretation'=>$request->interpretation

    ]);


    // Remove old values
    $result->details()->delete();



    foreach($request->parameters as $parameter)
    {

        ResultLaboDetail::create([

            'result_labo_id'=>$result->id,

            'parameter'=>$parameter['name'],

            'value'=>$parameter['value'],

            'status'=>$parameter['status'],

            'reference_range'=>$parameter['range'],

            'is_archive'=>false

        ]);

    }

    // Check if all items in this request have results
    $examRequest = $result->examRequestItem->examRequest;
    $allItemsCompleted = true;
    foreach ($examRequest->items as $reqItem) {
        if (!$reqItem->resultLabo()->exists()) {
            $allItemsCompleted = false;
            break;
        }
    }

    if ($allItemsCompleted) {
        $examRequest->update([
            'status' => 'completed'
        ]);
    }

    return redirect()
        ->route('center.exam-requests')
        ->with('success','Résultat modifié avec succès.');

}

}