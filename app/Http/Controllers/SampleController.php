<?php

namespace App\Http\Controllers;

use App\Models\Sample;
use App\Models\SampleBarcodeLog;
use App\Models\ExamRequestItem;
use App\Models\ExamRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SampleController extends Controller
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

    public function index()
    {
        $lab = auth()->user()->staff->laboratory;
        $samples = Sample::where('labo_id', $lab->id)
            ->with(['patient.user', 'examRequestItem.exam', 'collector.user'])
            ->latest()
            ->paginate(15);
        return view('center.samples.index', compact('samples'));
    }

    public function create()
    {
        $lab = auth()->user()->staff->laboratory;
        $collectedItems = ExamRequestItem::whereHas('examRequest', function ($q) use ($lab) {
            $q->where('labo_id', $lab->id)->whereIn('status', ['collected', 'processing']);
        })->whereDoesntHave('sample')
            ->with(['exam', 'examRequest.patient.user'])
            ->get();
        return view('center.samples.create', compact('collectedItems'));
    }

    public function store(Request $request)
    {
        $lab = auth()->user()->staff->laboratory;

        $validated = $request->validate([
            'exam_request_item_id' => 'required|exists:exam_request_items,id',
            'material_type' => 'nullable|string|max:50',
            'storage_location' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $item = ExamRequestItem::with('examRequest.patient')->findOrFail($validated['exam_request_item_id']);

        if ($item->examRequest->labo_id !== $lab->id) abort(403);

        DB::transaction(function () use ($validated, $item, $lab, &$sample) {
            $sampleCode = 'SMP-' . $lab->id . '-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            $sample = Sample::create([
                'sample_code' => $sampleCode,
                'exam_request_item_id' => $item->id,
                'patient_id' => $item->examRequest->patient_id,
                'labo_id' => $lab->id,
                'material_type' => $validated['material_type'] ?? $item->material_type,
                'status' => 'pending',
                'storage_location' => $validated['storage_location'] ?? null,
                'expiry_date' => $validated['expiry_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            SampleBarcodeLog::create([
                'sample_id' => $sample->id,
                'action' => 'created',
                'staff_id' => auth()->user()->staff->id,
                'notes' => 'Échantillon créé',
            ]);
        });

        return redirect()->route('center.samples.show', $sample->id)
            ->with('success', 'Échantillon créé avec succès. Code: ' . $sample->sample_code);
    }

    public function show(Sample $sample)
    {
        $lab = auth()->user()->staff->laboratory;
        if ($sample->labo_id !== $lab->id) abort(403);
        $sample->load(['patient.user', 'examRequestItem.exam', 'collector.user', 'barcodeLogs.staff.user']);
        return view('center.samples.show', compact('sample'));
    }

    public function updateStatus(Request $request, Sample $sample)
    {
        $lab = auth()->user()->staff->laboratory;
        if ($sample->labo_id !== $lab->id) abort(403);

        $validated = $request->validate([
            'status' => 'required|in:pending,collected,in_transit,received,processing,completed,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string',
            'storage_location' => 'nullable|string|max:100',
        ]);

        $data = ['status' => $validated['status']];
        if ($validated['status'] === 'collected') {
            $data['collected_by'] = auth()->user()->staff->id;
            $data['collection_date'] = now()->format('Y-m-d');
            $data['collection_time'] = now()->format('H:i');
        }
        if ($validated['status'] === 'rejected') {
            $data['rejection_reason'] = $validated['rejection_reason'];
        }
        if (isset($validated['storage_location'])) {
            $data['storage_location'] = $validated['storage_location'];
        }

        $sample->update($data);

        SampleBarcodeLog::create([
            'sample_id' => $sample->id,
            'action' => $validated['status'],
            'staff_id' => auth()->user()->staff->id,
            'location' => $validated['storage_location'] ?? $sample->storage_location,
        ]);

        return redirect()->route('center.samples.show', $sample->id)
            ->with('success', 'Statut de l\'échantillon mis à jour.');
    }

    public function printBarcode(Sample $sample)
    {
        $lab = auth()->user()->staff->laboratory;
        if ($sample->labo_id !== $lab->id) abort(403);
        $sample->load('patient.user');
        return view('center.samples.barcode', compact('sample'));
    }

    public function scan()
    {
        return view('center.samples.scan');
    }

    public function lookupByBarcode(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $sample = Sample::where('sample_code', $request->code)
            ->with(['patient.user', 'examRequestItem.exam', 'labo'])
            ->first();

        if (!$sample) {
            return response()->json(['error' => 'Échantillon non trouvé.'], 404);
        }

        $lab = auth()->user()->staff->laboratory;
        if ($sample->labo_id !== $lab->id) {
            return response()->json(['error' => 'Accès non autorisé.'], 403);
        }

        SampleBarcodeLog::create([
            'sample_id' => $sample->id,
            'action' => 'scanned',
            'staff_id' => auth()->user()->staff->id,
            'location' => 'Barcode scan',
        ]);

        return response()->json([
            'id' => $sample->id,
            'sample_code' => $sample->sample_code,
            'status' => $sample->status,
            'patient_name' => $sample->patient->user->first_name . ' ' . $sample->patient->user->last_name,
            'exam' => $sample->examRequestItem->exam->name ?? 'N/A',
            'material_type' => $sample->material_type,
            'storage_location' => $sample->storage_location,
            'show_url' => route('center.samples.show', $sample->id),
        ]);
    }
}
