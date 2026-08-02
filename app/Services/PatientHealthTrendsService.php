<?php

namespace App\Services;

use App\Models\ExamRequestItem;
use App\Models\Patient;
use App\Models\ResultLaboDetail;

class PatientHealthTrendsService
{
    private Patient $patient;

    /**
     * Create the trends service for a given patient.
     *
     * @param  Patient  $patient  the patient whose history to analyze
     */
    public function __construct(Patient $patient)
    {
        $this->patient = $patient;
    }

    /**
     * Build per-parameter time series and statistics for the patient.
     *
     * @return array list of parameter series with data points and stats
     */
    public function getTrends(): array
    {
        // Load completed exam results with their details
        $completedItems = ExamRequestItem::whereHas('examRequest', fn ($q) => $q
            ->where('patient_id', $this->patient->id)
            ->where('status', 'completed')
        )
            ->whereHas('resultLabo')
            ->with(['resultLabo.details', 'exam'])
            ->get();

        // Group every measured value by parameter
        $parameterHistory = [];
        foreach ($completedItems as $item) {
            if (! $item->resultLabo) {
                continue;
            }
            foreach ($item->resultLabo->details as $detail) {
                $param = $detail->parameter;
                if (! isset($parameterHistory[$param])) {
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

        // Sort points chronologically and compute per-parameter statistics
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

    /**
     * Compute aggregate health statistics for the patient.
     *
     * @return array summary counts and rates
     */
    public function getSummary(): array
    {
        // Count request and completed-result totals
        $totalRequests = $this->patient->examRequests()->count();
        $completedRequests = $this->patient->examRequests()->where('status', 'completed')->count();
        $totalResults = ExamRequestItem::whereHas('examRequest', fn ($q) => $q
            ->where('patient_id', $this->patient->id)
            ->where('status', 'completed')
        )->count();

        // Count abnormal result details across all patient exams
        $abnormalResults = ResultLaboDetail::whereHas('resultLabo.examRequestItem.examRequest', fn ($q) => $q
            ->where('patient_id', $this->patient->id)
        )
            ->whereIn('status', ['high', 'low', 'abnormal'])
            ->count();

        // Find the most recent completed exam
        $lastExam = $this->patient->examRequests()
            ->where('status', 'completed')
            ->latest('created_at')
            ->first();

        // Count distinct exams the patient has done
        $uniqueExams = ExamRequestItem::whereHas('examRequest', fn ($q) => $q
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

    /**
     * Classify a series of values as rising, falling or stable.
     *
     * @param  array  $values  chronological numeric values
     * @return string 'rising', 'falling' or 'stable'
     */
    private function calculateTrend(array $values): string
    {
        $count = count($values);

        // Need at least two points to detect a trend
        if ($count < 2) {
            return 'stable';
        }

        // Compare the average of the first half vs the second half
        $first = array_slice($values, 0, max(1, intdiv($count, 2)));
        $second = array_slice($values, intdiv($count, 2));

        $avgFirst = array_sum($first) / count($first);
        $avgSecond = array_sum($second) / count($second);

        $diff = $avgSecond - $avgFirst;

        // Ignore changes below 5% of the first half average
        $threshold = abs($avgFirst) * 0.05;

        if ($diff > $threshold) {
            return 'rising';
        }
        if ($diff < -$threshold) {
            return 'falling';
        }

        return 'stable';
    }
}
