<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AvailableExam;
use App\Models\Exam;
use App\Models\Labo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvailableExamController extends Controller
{
    /**
     * List available exams, optionally filtered by search keyword or laboratory.
     *
     * @return View
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $laboId = $request->input('labo_id', '');

        $query = AvailableExam::with(['labo', 'exam'])
            ->where('is_archive', false);

        // narrow results by exam name or code
        if ($search) {
            $query->whereHas('exam', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // narrow results by laboratory
        if ($laboId) {
            $query->where('labo_id', $laboId);
        }

        $availableExams = $query->orderBy('created_at', 'desc')->paginate(15)->appends($request->query());
        $labos = Labo::where('is_archive', false)->orderBy('name')->get();

        return view('admin.available-exams.index', compact('availableExams', 'labos', 'search', 'laboId'));
    }

    public function create()
    {
        $labos = Labo::where('is_archive', false)->orderBy('name')->get();
        $exams = Exam::where('is_archive', false)->orWhereNull('is_archive')->orderBy('name')->get();

        return view('admin.available-exams.create', compact('labos', 'exams'));
    }

    /**
     * Store a new available exam for a laboratory.
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        // validate the available exam fields
        $data = $request->validate([
            'labo_id' => 'required|exists:labos,id',
            'exam_id' => 'required|exists:exams,id',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // prevent duplicate active entries for the same lab and exam
        $exists = AvailableExam::where('labo_id', $data['labo_id'])
            ->where('exam_id', $data['exam_id'])
            ->where('is_archive', false)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Cet examen est déjà configuré pour ce laboratoire.');
        }

        AvailableExam::create($data);

        // redirect back to the available exams list
        return redirect()->route('admin.available-exams.index')
            ->with('success', 'Examen disponible ajouté avec succès.');
    }

    public function edit(AvailableExam $availableExam)
    {
        $labos = Labo::where('is_archive', false)->orderBy('name')->get();
        $exams = Exam::where('is_archive', false)->orWhereNull('is_archive')->orderBy('name')->get();

        return view('admin.available-exams.edit', compact('availableExam', 'labos', 'exams'));
    }

    /**
     * Update the price and activation of an available exam.
     *
     * @return RedirectResponse
     */
    public function update(Request $request, AvailableExam $availableExam)
    {
        // validate the price and activation flag
        $data = $request->validate([
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $availableExam->update($data);

        // redirect back to the available exams list
        return redirect()->route('admin.available-exams.index')
            ->with('success', 'Examen disponible mis à jour.');
    }

    /**
     * Toggle the archived status of an available exam.
     *
     * @return RedirectResponse
     */
    public function archive(AvailableExam $availableExam)
    {
        $availableExam->update(['is_archive' => ! $availableExam->is_archive]);

        return back()->with('success', 'Statut modifié.');
    }
}
