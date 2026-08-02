<?php

namespace App\Http\Controllers;

use App\Models\ExamRequestItem;
use App\Services\MachineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class MachineController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! auth()->check() || ! auth()->user()->staff) {
                return redirect()->route('home');
            }

            return $next($request);
        });
    }

    /**
     * Send an exam request item to the lab machine and process the returned results.
     *
     * @return RedirectResponse
     */
    public function sendToMachine(ExamRequestItem $item)
    {
        $lab = auth()->user()->staff->laboratory;

        // the item must belong to the current laboratory
        if ($item->examRequest->labo_id !== $lab->id) {
            abort(403);
        }

        // block sending when a result already exists
        if ($item->resultLabo) {
            return back()->with('error', 'Un résultat existe déjà pour cet examen.');
        }

        // block sending once the doctor validated the results
        if ($item->examRequest->approved_by_doctor) {
            return back()->with('error', 'Impossible d\'envoyer à la machine — le médecin a déjà validé et interprété les résultats de cette demande.');
        }

        // use the first active machine configuration of the laboratory
        $machineConfig = $lab->machineConfigurations()->active()->where('enabled', true)->first();

        try {
            // send the order and store the machine-produced results
            $machine = new MachineService($machineConfig);
            $response = $machine->sendOrder($item);
            $machine->processResults($item, $response, auth()->user()->staff->id);

            $processingTime = $response['processing_time_seconds'] ?? '?';
            $source = $response['source'] ?? 'unknown';
            $configLabel = $machineConfig ? " ({$machineConfig->name})" : '';

            return back()->with('success', "Résultats reçus{$configLabel} ({$source}) en {$processingTime}s pour : {$item->exam->name}");
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur machine : '.$e->getMessage());
        }
    }

    /**
     * Return the online status and info of the active machine as JSON.
     *
     * @return JsonResponse
     */
    public function status()
    {
        $lab = auth()->user()->staff->laboratory;
        $machineConfig = $lab->machineConfigurations()->active()->where('enabled', true)->first();

        $machine = new MachineService($machineConfig);

        return response()->json([
            'online' => $machine->isOnline(),
            'info' => $machine->getStatus(),
            'config_name' => $machineConfig?->name,
        ]);
    }
}
