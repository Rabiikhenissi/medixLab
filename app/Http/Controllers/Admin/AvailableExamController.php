<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AvailableExam;
use App\Models\Labo;
use App\Models\Exam;
use Illuminate\Http\Request;

class AvailableExamController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $laboId = $request->input('labo_id', '');

        $query = AvailableExam::with(['labo', 'exam'])
            ->where('is_archive', false);

        if ($search) {
            $query->whereHas('exam', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

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

    public function store(Request $request)
    {
        $data = $request->validate([
            'labo_id' => 'required|exists:labos,id',
            'exam_id' => 'required|exists:exams,id',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $exists = AvailableExam::where('labo_id', $data['labo_id'])
            ->where('exam_id', $data['exam_id'])
            ->where('is_archive', false)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Cet examen est déjà configuré pour ce laboratoire.');
        }

        AvailableExam::create($data);

        return redirect()->route('admin.available-exams.index')
            ->with('success', 'Examen disponible ajouté avec succès.');
    }

    public function edit(AvailableExam $availableExam)
    {
        $labos = Labo::where('is_archive', false)->orderBy('name')->get();
        $exams = Exam::where('is_archive', false)->orWhereNull('is_archive')->orderBy('name')->get();

        return view('admin.available-exams.edit', compact('availableExam', 'labos', 'exams'));
    }

    public function update(Request $request, AvailableExam $availableExam)
    {
        $data = $request->validate([
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $availableExam->update($data);

        return redirect()->route('admin.available-exams.index')
            ->with('success', 'Examen disponible mis à jour.');
    }

    public function archive(AvailableExam $availableExam)
    {
        $availableExam->update(['is_archive' => !$availableExam->is_archive]);

        return back()->with('success', 'Statut modifié.');
    }
}
