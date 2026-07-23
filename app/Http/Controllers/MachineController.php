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

        $machine = new MachineService();

        try {
            $response = $machine->sendOrder($item);
            $machine->processResults($item, $response, auth()->user()->staff->id);

            $processingTime = $response['processing_time_seconds'] ?? '?';
            $source = $response['source'] ?? 'unknown';

            return back()->with('success', "Résultats reçus ({$source}) en {$processingTime}s pour : {$item->exam->name}");
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
