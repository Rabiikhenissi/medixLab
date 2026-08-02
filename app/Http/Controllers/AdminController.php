<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Exam;
use App\Models\ExamParameter;
use App\Models\ExamRequest;
use App\Models\ExamRequestItem;
use App\Models\Labo;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Render the admin dashboard with cached statistics, charts and recent activity.
     *
     * @return View|RedirectResponse
     */
    public function dashboard()
    {
        // only admins can access the dashboard
        if (! auth()->user()->admin) {
            return redirect()->route('home');
        }

        // Cache admin dashboard for 5 minutes
        $cacheKey = 'admin_dashboard_v2';
        $cached = cache()->remember($cacheKey, 300, function () {
            $stats = [
                'total_exams' => Exam::where(function ($q) {
                    $q->where('is_archive', false)
                        ->orWhereNull('is_archive');
                })->count(),
                'total_patients' => Patient::count(),
                'total_doctors' => Doctor::count(),
                'total_laboratories' => Labo::where('is_archive', false)->count(),
                'total_exam_requests' => ExamRequest::count(),
                'archived_exams' => Exam::where('is_archive', true)->count(),
            ];

            // 1. Status distribution — single aggregated query
            $statusCounts = ExamRequest::selectRaw("
                    COALESCE(SUM(status = 'pending'), 0) as pending,
                    COALESCE(SUM(status = 'assigned'), 0) as assigned,
                    COALESCE(SUM(status = 'collected'), 0) as collected,
                    COALESCE(SUM(status = 'processing'), 0) as processing,
                    COALESCE(SUM(status = 'completed'), 0) as completed,
                    COALESCE(SUM(status = 'cancelled'), 0) as cancelled
                ")->first();

            $statusDistribution = [
                'pending' => (int) $statusCounts->pending,
                'assigned' => (int) $statusCounts->assigned,
                'collected' => (int) $statusCounts->collected,
                'processing' => (int) $statusCounts->processing,
                'completed' => (int) $statusCounts->completed,
                'cancelled' => (int) $statusCounts->cancelled,
            ];

            // 2. Top 5 most prescribed exams
            $topExams = ExamRequestItem::select('exam_id', \DB::raw('count(*) as count'))
                ->groupBy('exam_id')
                ->orderByDesc('count')
                ->limit(5)
                ->with('exam')
                ->get()
                ->map(fn($item) => ['name' => $item->exam?->name ?? 'Inconnu', 'count' => (int) $item->count])
                ->values()
                ->all();

            // 3. Requests volume over the last 15 days
            $dailyVolume = ExamRequest::where('created_at', '>=', Carbon::now()->subDays(14)->startOfDay())
                ->selectRaw('DATE(created_at) as date, count(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count', 'date')
                ->toArray();

            $chartData = [];
            for ($i = 14; $i >= 0; $i--) {
                $dateString = Carbon::now()->subDays($i)->toDateString();
                $chartData[] = [
                    'label' => Carbon::now()->subDays($i)->format('d M'),
                    'count' => $dailyVolume[$dateString] ?? 0,
                ];
            }

            // 4. Recent prescriptions (last 5)
            $recentPrescriptions = ExamRequest::with(['doctor.user', 'patient.user', 'laboratory'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn($rx) => [
                    'id'           => $rx->id,
                    'status'       => $rx->status,
                    'created_at'   => $rx->created_at->diffForHumans(),
                    'doctor_name'  => $rx->doctor?->user ? trim($rx->doctor->user->first_name . ' ' . $rx->doctor->user->last_name) : '—',
                    'patient_name' => $rx->patient?->user ? trim($rx->patient->user->first_name . ' ' . $rx->patient->user->last_name) : '—',
                    'lab_name'     => $rx->laboratory?->name ?? 'Non assigné',
                ])
                ->values()
                ->all();

            // 5. Labs per city
            $labsPerCity = Labo::where('is_archive', false)
                ->selectRaw('city, count(*) as count')
                ->groupBy('city')
                ->orderByDesc('count')
                ->limit(5)
                ->pluck('count', 'city')
                ->toArray();

            // 6. Active labs count
            $activeLabs = Labo::where('is_archive', false)->count();

            // 7. Today's prescriptions count
            $todayPrescriptions = ExamRequest::whereDate('created_at', Carbon::today())->count();

            return compact(
                'stats', 'statusDistribution', 'topExams', 'chartData',
                'recentPrescriptions', 'labsPerCity', 'activeLabs', 'todayPrescriptions'
            );
        });

        return view('admin.dashboard', array_merge(
            ['user' => auth()->user()],
            $cached
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Exams Pages
    |--------------------------------------------------------------------------
    */

    /**
     * List exams, optionally filtered by search keyword, category and archive status.
     *
     * @return View
     */
    public function exams(Request $request)
    {

        // only admins can access
        if (! auth()->user()->admin) {
            abort(403);
        }

        $showArchived = $request->boolean('show_archived');

        $search = $request->input('search', '');

        $category = $request->input('category', '');

        $query = Exam::query();

        // hide archived exams unless explicitly requested
        if (! $showArchived) {

            $query->where(function ($q) {

                $q->where('is_archive', false)
                    ->orWhereNull('is_archive');
            });
        }

        // narrow results by search keyword
        if ($search) {

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // narrow results by selected category
        if ($category) {

            $query->where(
                'category',
                $category
            );
        }

        $exams = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.exams.index', [

            'exams' => $exams,

            'showArchived' => $showArchived,

            'search' => $search,

            'selectedCategory' => $category,

        ]);
    }

    public function createExam()
    {

        if (! auth()->user()->admin) {
            abort(403);
        }

        return view('admin.exams.create');
    }

    public function editExam(Exam $exam)
    {

        if (! auth()->user()->admin) {
            abort(403);
        }

        $exam->load('parameters');

        return view(
            'admin.exams.edit',
            compact('exam')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Exams Actions
    |--------------------------------------------------------------------------
    */

    /**
     * Validate and persist a new exam together with its optional parameters.
     *
     * @return RedirectResponse
     */
    public function storeExam(Request $request)
    {

        // only admins can access
        if (! auth()->user()->admin) {
            abort(403);
        }

        // validate exam fields
        $data = $request->validate([

            'code' => 'required|string|max:255|unique:exams',

            'name' => 'required|string|max:255',

            'category' => 'required|in:biochemistry,hematology,microbiology,immunology,urinalysis,other',

            'description' => 'nullable|string',

            'default_normal_range' => 'nullable|string|max:255',

            'preparation_instructions' => 'nullable|string',

            'parameters' => 'nullable|array',

            'parameters.*.name' => 'required|string|max:255',

            'parameters.*.unit' => 'nullable|string|max:255',

            'parameters.*.normal_range' => 'nullable|string|max:255',

        ]);

        // create the exam as not archived
        $data['is_archive'] = false;

        $exam = Exam::create($data);

        // persist any submitted exam parameters
        if ($request->has('parameters')) {

            foreach ($request->parameters as $parameter) {

                ExamParameter::create([

                    'exam_id' => $exam->id,

                    'name' => $parameter['name'],

                    'unit' => $parameter['unit'] ?? null,

                    'normal_range' => $parameter['normal_range'] ?? null,

                    'is_archive' => false,

                ]);
            }
        }

        // redirect back to the exam list
        return redirect()

            ->route('admin.exams.index')

            ->with(
                'success',
                'Examen créé avec succès.'
            );
    }

    /**
     * Validate and update the given exam.
     *
     * @return RedirectResponse
     */
    public function updateExam(Request $request, Exam $exam)
    {

        // only admins can access
        if (! auth()->user()->admin) {
            abort(403);
        }

        // validate exam fields
        $data = $request->validate([

            'code' => 'required|string|max:255|unique:exams,code,'.$exam->id,

            'name' => 'required|string|max:255',

            'category' => 'required|in:biochemistry,hematology,microbiology,immunology,urinalysis,other',

            'description' => 'nullable|string',

            'default_normal_range' => 'nullable|string|max:255',

            'preparation_instructions' => 'nullable|string',

        ]);

        // apply the changes to the exam
        $exam->update($data);

        // redirect back to the exam list
        return redirect()

            ->route('admin.exams.index')

            ->with(
                'success',
                'Examen mis à jour avec succès.'
            );
    }

    public function showExam(Exam $exam)
    {
        if (! auth()->user()->admin) {
            abort(403);
        }

        $exam->load('parameters');

        return view('admin.exams.show', [

            'exam' => $exam,

        ]);
    }

    public function archiveExam(Exam $exam)
    {

        if (! auth()->user()->admin) {
            abort(403);
        }

        $exam->update([

            'is_archive' => ! $exam->is_archive,

        ]);

        return redirect()

            ->route('admin.exams.index')

            ->with(
                'success',
                'Statut de l\'examen modifié.'
            );
    }

    public function forceDeleteExam(Exam $exam)
    {
        if (! auth()->user()->admin) {
            abort(403);
        }

        $exam->delete();

        return redirect()
            ->route('admin.exams.index')
            ->with('success', 'Examen supprimé définitivement.');
    }
}
