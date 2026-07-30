<?php

namespace App\Http\Controllers;

use App\Models\ExamRequestItem;
use App\Services\MachineService;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !auth()->user()->staff) {
                return redirect()->route('home');
            }
            return $next($request);
        });
    }

    public function sendToMachine(ExamRequestItem $item)
    {
        $lab = auth()->user()->staff->laboratory;

        if ($item->examRequest->labo_id !== $lab->id) {
            abort(403);
        }

        if ($item->resultLabo) {
            return back()->with('error', 'Un résultat existe déjà pour cet examen.');
        }

        if ($item->examRequest->approved_by_doctor) {
            return back()->with('error', 'Impossible d\'envoyer à la machine — le médecin a déjà validé et interprété les résultats de cette demande.');
        }

        $machineConfig = $lab->machineConfigurations()->active()->where('enabled', true)->first();

        try {
            $machine = new MachineService($machineConfig);
            $response = $machine->sendOrder($item);
            $machine->processResults($item, $response, auth()->user()->staff->id);

            $processingTime = $response['processing_time_seconds'] ?? '?';
            $source = $response['source'] ?? 'unknown';
            $configLabel = $machineConfig ? " ({$machineConfig->name})" : '';

            return back()->with('success', "Résultats reçus{$configLabel} ({$source}) en {$processingTime}s pour : {$item->exam->name}");
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur machine : ' . $e->getMessage());
        }
    }

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
