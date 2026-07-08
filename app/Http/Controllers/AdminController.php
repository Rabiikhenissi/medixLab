<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Labo;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Admin dashboard with stats and exam management.
     */
    public function dashboard(Request $request)
    {
        // Verify admin access
        if (!auth()->user()->admin) {
            return redirect()->route('home');
        }

        $showArchived = $request->boolean('show_archived');
        $search = $request->input('search', '');
        $category = $request->input('category', '');

        $query = Exam::query();

        if (!$showArchived) {
            $query->where(function ($q) {
                $q->where('is_archive', false)->orWhereNull('is_archive');
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        $exams = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());

        $stats = [
            'total_exams' => Exam::where(function ($q) {
                $q->where('is_archive', false)->orWhereNull('is_archive');
            })->count(),
            'total_patients' => Patient::count(),
            'total_doctors' => Doctor::count(),
            'archived_exams' => Exam::where('is_archive', true)->count(),
        ];

        return view('admin.dashboard', [
            'user' => auth()->user(),
            'exams' => $exams,
            'stats' => $stats,
            'showArchived' => $showArchived,
            'search' => $search,
            'selectedCategory' => $category,
        ]);
    }

    /**
     * Store a new exam.
     */
    public function storeExam(Request $request)
    {
        if (!auth()->user()->admin) {
            abort(403);
        }

        $data = $request->validate([
            'code' => 'required|string|max:255|unique:exams',
            'name' => 'required|string|max:255',
            'category' => 'required|in:biochemistry,hematology,microbiology,immunology,urinalysis,other',
            'description' => 'nullable|string',
            'default_normal_range' => 'nullable|string|max:255',
            'preparation_instructions' => 'nullable|string',
        ]);

        $data['is_archive'] = false;
        Exam::create($data);

        return redirect()->route('admin.dashboard')->with('success', 'Examen créé avec succès.');
    }

    /**
     * Update an existing exam.
     */
    public function updateExam(Request $request, Exam $exam)
    {
        if (!auth()->user()->admin) {
            abort(403);
        }

        $data = $request->validate([
            'code' => 'required|string|max:255|unique:exams,code,' . $exam->id,
            'name' => 'required|string|max:255',
            'category' => 'required|in:biochemistry,hematology,microbiology,immunology,urinalysis,other',
            'description' => 'nullable|string',
            'default_normal_range' => 'nullable|string|max:255',
            'preparation_instructions' => 'nullable|string',
        ]);

        $exam->update($data);

        return redirect()->route('admin.dashboard')->with('success', 'Examen mis à jour avec succès.');
    }

    /**
     * Toggle archive status of an exam.
     */
    public function archiveExam(Exam $exam)
    {
        if (!auth()->user()->admin) {
            abort(403);
        }

        $exam->update(['is_archive' => !$exam->is_archive]);

        $message = $exam->is_archive
            ? 'Examen archivé avec succès.'
            : 'Examen restauré avec succès.';

        return redirect()->route('admin.dashboard')->with('success', $message);
    }
}
