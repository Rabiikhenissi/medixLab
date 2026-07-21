<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\ExamRequestItem;
use App\Models\ResultLaboDetail;

class PatientHealthTrendsService
{
    private Patient $patient;

    public function __construct(Patient $patient)
    {
        $this->patient = $patient;
    }

    public function getTrends(): array
    {
        $completedItems = ExamRequestItem::whereHas('examRequest', fn($q) => $q
            ->where('patient_id', $this->patient->id)
            ->where('status', 'completed')
        )
        ->whereHas('resultLabo')
        ->with(['resultLabo.details', 'exam'])
        ->get();

        $parameterHistory = [];
        foreach ($completedItems as $item) {
            if (!$item->resultLabo) continue;
            foreach ($item->resultLabo->details as $detail) {
                $param = $detail->parameter;
                if (!isset($parameterHistory[$param])) {
                    $parameterHistory[$param] = [
                        'parameter' => $param,
                        'unit' => $detail->unit ?? '',
                        'data_points' => [],
                    ];
                }
                $parameterHistory[$param]['data_points'][] = [
                    'date' => $item->created_at->format('Y-m-d'),
                    'value' => (float) $detail->value,
                    'status' => $detail->status,
                    'reference_range' => $detail->reference_range,
                    'exam_name' => $item->exam->name ?? '',
                ];
            }
        }

        foreach ($parameterHistory as &$param) {
            $param['data_points'] = collect($param['data_points'])
                ->sortBy('date')
                ->values()
                ->toArray();

            $values = collect($param['data_points'])->pluck('value');
            $param['stats'] = [
                'count' => $values->count(),
                'min' => $values->min(),
                'max' => $values->max(),
                'avg' => round($values->avg(), 2),
                'latest' => $values->last(),
                'trend' => $this->calculateTrend($values->toArray()),
                'abnormal_count' => collect($param['data_points'])
                    ->whereIn('status', ['high', 'low', 'abnormal'])->count(),
            ];
        }
        unset($param);

        return array_values($parameterHistory);
    }

    public function getSummary(): array
    {
        $totalRequests = $this->patient->examRequests()->count();
        $completedRequests = $this->patient->examRequests()->where('status', 'completed')->count();
        $totalResults = ExamRequestItem::whereHas('examRequest', fn($q) => $q
            ->where('patient_id', $this->patient->id)
            ->where('status', 'completed')
        )->count();

        $abnormalResults = ResultLaboDetail::whereHas('resultLabo.examRequestItem.examRequest', fn($q) => $q
            ->where('patient_id', $this->patient->id)
        )
        ->whereIn('status', ['high', 'low', 'abnormal'])
        ->count();

        $lastExam = $this->patient->examRequests()
            ->where('status', 'completed')
            ->latest('created_at')
            ->first();

        $uniqueExams = ExamRequestItem::whereHas('examRequest', fn($q) => $q
            ->where('patient_id', $this->patient->id)
        )
        ->with('exam')
        ->get()
        ->pluck('exam.name')
        ->filter()
        ->unique()
        ->count();

        return [
            'total_requests' => $totalRequests,
            'completed_requests' => $completedRequests,
            'completion_rate' => $totalRequests > 0 ? round(($completedRequests / $totalRequests) * 100) : 0,
            'total_results' => $totalResults,
            'abnormal_results' => $abnormalResults,
            'abnormal_rate' => $totalResults > 0 ? round(($abnormalResults / $totalResults) * 100) : 0,
            'unique_exams' => $uniqueExams,
            'last_exam_date' => $lastExam?->created_at?->format('d/m/Y'),
        ];
    }

    private function calculateTrend(array $values): string
    {
        $count = count($values);
        if ($count < 2) return 'stable';

        $first = array_slice($values, 0, max(1, intdiv($count, 2)));
        $second = array_slice($values, intdiv($count, 2));

        $avgFirst = array_sum($first) / count($first);
        $avgSecond = array_sum($second) / count($second);

        $diff = $avgSecond - $avgFirst;
        $threshold = abs($avgFirst) * 0.05;

        if ($diff > $threshold) return 'rising';
        if ($diff < -$threshold) return 'falling';
        return 'stable';
    }
}
