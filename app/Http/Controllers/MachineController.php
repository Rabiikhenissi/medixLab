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

        $machine = new MachineService();

        if (!$machine->isOnline()) {
            return back()->with('error', 'La machine est hors ligne. Vérifiez que le simulateur est démarré (python lab_simulator\simulator.py).');
        }

        try {
            $response = $machine->sendOrder($item);
            $machine->processResults($item, $response, auth()->user()->staff->id);

            $processingTime = $response['processing_time_seconds'] ?? '?';

            return back()->with('success', "Résultats reçus de la machine en {$processingTime}s pour : {$item->exam->name}");
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur machine : ' . $e->getMessage());
        }
    }

    public function status()
    {
        $machine = new MachineService();

        return response()->json([
            'online' => $machine->isOnline(),
            'info' => $machine->getStatus(),
        ]);
    }
}
