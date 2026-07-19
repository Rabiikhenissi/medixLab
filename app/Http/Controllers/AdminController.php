<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\ExamParameter;
use Illuminate\Http\Request;

use App\Models\Labo;
use App\Models\ExamRequest;
use App\Models\ExamRequestItem;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{


    public function dashboard()
    {

        if (!auth()->user()->admin) {
            return redirect()->route('home');
        }


        $stats = [

            'total_exams' => Exam::where(function($q){

                $q->where('is_archive', false)
                  ->orWhereNull('is_archive');

            })->count(),


            'total_patients' => Patient::count(),


            'total_doctors' => Doctor::count(),
            'total_laboratories' => Labo::where('is_archive', false)->count(),
            'total_exam_requests' => ExamRequest::count(),


            'archived_exams' => Exam::where('is_archive', true)->count(),

        ];

        // 1. Status distribution
        $statusDistribution = [
            'pending' => ExamRequest::where('status', 'pending')->count(),
            'assigned' => ExamRequest::where('status', 'assigned')->count(),
            'collected' => ExamRequest::where('status', 'collected')->count(),
            'processing' => ExamRequest::where('status', 'processing')->count(),
            'completed' => ExamRequest::where('status', 'completed')->count(),
            'cancelled' => ExamRequest::where('status', 'cancelled')->count(),
        ];

        // 2. Top 5 most prescribed exams
        $topExams = ExamRequestItem::select('exam_id', \DB::raw('count(*) as count'))
            ->groupBy('exam_id')
            ->orderByDesc('count')
            ->limit(5)
            ->with('exam')
            ->get();

        // 3. Requests volume over the last 15 days (daily volume)
        $dailyVolume = ExamRequest::where('created_at', '>=', Carbon::now()->subDays(14)->startOfDay())
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $chartData = [];
        for ($i = 14; $i >= 0; $i--) {
            $dateString = Carbon::now()->subDays($i)->toDateString();
            $label = Carbon::now()->subDays($i)->format('d M');
            $chartData[] = [
                'label' => $label,
                'count' => $dailyVolume[$dateString] ?? 0
            ];
        }



        return view('admin.dashboard',[

            'user'=>auth()->user(),

            'stats'=>$stats,
            'statusDistribution' => $statusDistribution,
            'topExams' => $topExams,
            'chartData' => $chartData,

        ]);

    }







    /*
    |--------------------------------------------------------------------------
    | Exams Pages
    |--------------------------------------------------------------------------
    */



    public function exams(Request $request)
    {


        if (!auth()->user()->admin) {
            abort(403);
        }



        $showArchived = $request->boolean('show_archived');

        $search = $request->input('search','');

        $category = $request->input('category','');




        $query = Exam::query();





        if(!$showArchived)
        {

            $query->where(function($q){

                $q->where('is_archive',false)
                  ->orWhereNull('is_archive');

            });

        }





        if($search)
        {

            $query->where(function($q) use($search){

                $q->where('name','like',"%{$search}%")
                  ->orWhere('code','like',"%{$search}%")
                  ->orWhere('description','like',"%{$search}%");

            });

        }





        if($category)
        {

            $query->where(
                'category',
                $category
            );

        }





        $exams = $query
            ->orderBy('created_at','desc')
            ->paginate(10)
            ->appends($request->query());





        return view('admin.exams.index',[

            'exams'=>$exams,

            'showArchived'=>$showArchived,

            'search'=>$search,

            'selectedCategory'=>$category,

        ]);

    }









    public function createExam()
    {

        if (!auth()->user()->admin) {
            abort(403);
        }


        return view('admin.exams.create');

    }









    public function editExam(Exam $exam)
    {

        if (!auth()->user()->admin) {
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





    public function storeExam(Request $request)
    {


        if(!auth()->user()->admin)
        {
            abort(403);
        }



        $data=$request->validate([


            'code'=>'required|string|max:255|unique:exams',

            'name'=>'required|string|max:255',

            'category'=>'required|in:biochemistry,hematology,microbiology,immunology,urinalysis,other',

            'description'=>'nullable|string',

            'default_normal_range'=>'nullable|string|max:255',

            'preparation_instructions'=>'nullable|string',



            'parameters'=>'nullable|array',

            'parameters.*.name'=>'required|string|max:255',

            'parameters.*.unit'=>'nullable|string|max:255',

            'parameters.*.normal_range'=>'nullable|string|max:255',

        ]);





        $data['is_archive']=false;



        $exam = Exam::create($data);





        if($request->has('parameters'))
        {


            foreach($request->parameters as $parameter)
            {


                ExamParameter::create([

                    'exam_id'=>$exam->id,

                    'name'=>$parameter['name'],

                    'unit'=>$parameter['unit'] ?? null,

                    'normal_range'=>$parameter['normal_range'] ?? null,

                    'is_archive'=>false,

                ]);


            }

        }






        return redirect()

            ->route('admin.exams.index')

            ->with(
                'success',
                'Examen créé avec succès.'
            );

    }









    public function updateExam(Request $request, Exam $exam)
    {


        if(!auth()->user()->admin)
        {
            abort(403);
        }





        $data=$request->validate([


            'code'=>'required|string|max:255|unique:exams,code,'.$exam->id,

            'name'=>'required|string|max:255',

            'category'=>'required|in:biochemistry,hematology,microbiology,immunology,urinalysis,other',

            'description'=>'nullable|string',

            'default_normal_range'=>'nullable|string|max:255',

            'preparation_instructions'=>'nullable|string',


        ]);






        $exam->update($data);





        return redirect()

            ->route('admin.exams.index')

            ->with(
                'success',
                'Examen mis à jour avec succès.'
            );

    }








public function showExam(Exam $exam)
{
    if(!auth()->user()->admin)
    {
        abort(403);
    }


    $exam->load('parameters');


    return view('admin.exams.show', [

        'exam'=>$exam

    ]);
}
    public function archiveExam(Exam $exam)
    {


        if(!auth()->user()->admin)
        {
            abort(403);
        }





        $exam->update([

            'is_archive'=>!$exam->is_archive

        ]);





        return redirect()

            ->route('admin.exams.index')

            ->with(
                'success',
                'Statut de l’examen modifié.'
            );

    }



}