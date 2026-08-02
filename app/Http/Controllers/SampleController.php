<?php

namespace App\Http\Controllers;

use App\Models\ExamRequestItem;
use App\Models\Sample;
use App\Models\SampleBarcodeLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SampleController extends Controller
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
     * List the samples of the current user's laboratory, paginated.
     *
     * @return View
     */
    public function index()
    {
        // samples belong to the current user's laboratory
        $lab = auth()->user()->staff->laboratory;
        $samples = Sample::where('labo_id', $lab->id)
            ->with(['patient.user', 'examRequestItem.exam', 'collector.user'])
            ->latest()
            ->paginate(15);

        return view('center.samples.index', compact('samples'));
    }

    /**
     * Show the sample creation form for collected or processing exam items.
     *
     * @return View
     */
    public function create()
    {
        // only collected/processing items without an existing sample qualify
        $lab = auth()->user()->staff->laboratory;
        $collectedItems = ExamRequestItem::whereHas('examRequest', function ($q) use ($lab) {
            $q->where('labo_id', $lab->id)->whereIn('status', ['collected', 'processing']);
        })->whereDoesntHave('sample')
            ->with(['exam', 'examRequest.patient.user'])
            ->get();

        return view('center.samples.create', compact('collectedItems'));
    }

    /**
     * Create a sample for a given exam request item and log its barcode action.
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $lab = auth()->user()->staff->laboratory;

        // validate the sample fields
        $validated = $request->validate([
            'exam_request_item_id' => 'required|exists:exam_request_items,id',
            'material_type' => 'nullable|string|max:50',
            'storage_location' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // make sure the item belongs to this laboratory
        $item = ExamRequestItem::with('examRequest.patient')->findOrFail($validated['exam_request_item_id']);

        if ($item->examRequest->labo_id !== $lab->id) {
            abort(403);
        }

        DB::transaction(function () use ($validated, $item, $lab, &$sample) {
            // generate a unique sample code
            $sampleCode = 'SMP-'.$lab->id.'-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -5));

            // create the sample record
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

            // log the creation action in the barcode history
            SampleBarcodeLog::create([
                'sample_id' => $sample->id,
                'action' => 'created',
                'staff_id' => auth()->user()->staff->id,
                'notes' => 'Échantillon créé',
            ]);
        });

        // redirect to the new sample page
        return redirect()->route('center.samples.show', $sample->id)
            ->with('success', 'Échantillon créé avec succès. Code: '.$sample->sample_code);
    }

    /**
     * Display a single sample with its barcode history.
     *
     * @return View
     */
    public function show(Sample $sample)
    {
        $lab = auth()->user()->staff->laboratory;
        if ($sample->labo_id !== $lab->id) {
            abort(403);
        }
        $sample->load(['patient.user', 'examRequestItem.exam', 'collector.user', 'barcodeLogs.staff.user']);

        return view('center.samples.show', compact('sample'));
    }

    /**
     * Update the status of a sample and log the status change.
     *
     * @return RedirectResponse
     */
    public function updateStatus(Request $request, Sample $sample)
    {
        // make sure the sample belongs to this laboratory
        $lab = auth()->user()->staff->laboratory;
        if ($sample->labo_id !== $lab->id) {
            abort(403);
        }

        // validate the new status
        $validated = $request->validate([
            'status' => 'required|in:pending,collected,in_transit,received,processing,completed,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string',
            'storage_location' => 'nullable|string|max:100',
        ]);

        // add status-specific metadata
        $data = ['status' => $validated['status']];
        // record collector details when the sample is collected
        if ($validated['status'] === 'collected') {
            $data['collected_by'] = auth()->user()->staff->id;
            $data['collection_date'] = now()->format('Y-m-d');
            $data['collection_time'] = now()->format('H:i');
        }
        // record the reason when the sample is rejected
        if ($validated['status'] === 'rejected') {
            $data['rejection_reason'] = $validated['rejection_reason'];
        }
        if (isset($validated['storage_location'])) {
            $data['storage_location'] = $validated['storage_location'];
        }

        // persist the status and log the action
        $sample->update($data);

        SampleBarcodeLog::create([
            'sample_id' => $sample->id,
            'action' => $validated['status'],
            'staff_id' => auth()->user()->staff->id,
            'location' => $validated['storage_location'] ?? $sample->storage_location,
        ]);

        // redirect back to the sample page
        return redirect()->route('center.samples.show', $sample->id)
            ->with('success', 'Statut de l\'échantillon mis à jour.');
    }

    /**
     * Show the barcode printable page for a sample.
     *
     * @return View
     */
    public function printBarcode(Sample $sample)
    {
        $lab = auth()->user()->staff->laboratory;
        if ($sample->labo_id !== $lab->id) {
            abort(403);
        }
        $sample->load('patient.user');

        return view('center.samples.barcode', compact('sample'));
    }

    public function scan()
    {
        return view('center.samples.scan');
    }

    /**
     * Look up a sample by barcode and return its details as JSON.
     *
     * @return JsonResponse
     */
    public function lookupByBarcode(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        // find the sample by its code
        $sample = Sample::where('sample_code', $request->code)
            ->with(['patient.user', 'examRequestItem.exam', 'labo'])
            ->first();

        if (! $sample) {
            return response()->json(['error' => 'Échantillon non trouvé.'], 404);
        }

        // restrict access to the owning laboratory
        $lab = auth()->user()->staff->laboratory;
        if ($sample->labo_id !== $lab->id) {
            return response()->json(['error' => 'Accès non autorisé.'], 403);
        }

        // log the scan action
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
            'patient_name' => $sample->patient->user->first_name.' '.$sample->patient->user->last_name,
            'exam' => $sample->examRequestItem->exam->name ?? 'N/A',
            'material_type' => $sample->material_type,
            'storage_location' => $sample->storage_location,
            'show_url' => route('center.samples.show', $sample->id),
        ]);
    }
}
