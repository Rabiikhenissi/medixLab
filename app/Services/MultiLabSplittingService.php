<?php

namespace App\Services;

use App\Models\ExamRequest;
use App\Models\ExamRequestItem;
use App\Models\Labo;
use App\Models\AvailableExam;
use Illuminate\Support\Facades\DB;

class MultiLabSplittingService
{
    private ExamRequest $examRequest;

    public function __construct(ExamRequest $examRequest)
    {
        $this->examRequest = $examRequest;
    }

    public function getSplitSuggestions(): array
    {
        $requiredExamIds = $this->examRequest->items->pluck('exam_id')->toArray();

        if (empty($requiredExamIds)) return [];

        $labs = Labo::where('is_archive', false)
            ->with(['availableExams' => fn($q) => $q->where('is_active', true)])
            ->get();

        $grouped = $this->groupByLabCapability($labs, $requiredExamIds);
        $split = $this->buildOptimalSplit($grouped, $requiredExamIds);

        return $split;
    }

    public function assignSplit(array $assignments): bool
    {
        DB::beginTransaction();

        try {
            foreach ($assignments as $assignment) {
                $laboId = $assignment['labo_id'];
                $examIds = $assignment['exam_ids'];

                $items = $this->examRequest->items()
                    ->whereIn('exam_id', $examIds)
                    ->get();

                foreach ($items as $item) {
                    $item->update(['labo_id' => $laboId]);
                }
            }

            if ($this->examRequest->laboratory_id === null) {
                $firstLabId = $assignments[0]['labo_id'] ?? null;
                if ($firstLabId) {
                    $this->examRequest->update([
                        'labo_id' => $firstLabId,
                        'status' => 'assigned',
                    ]);
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Multi-lab split failed: ' . $e->getMessage());
            return false;
        }
    }

    private function groupByLabCapability($labs, array $requiredExamIds): array
    {
        $labCoverage = [];

        foreach ($labs as $lab) {
            $coveredExamIds = $lab->availableExams->pluck('exam_id')->toArray();
            $intersection = array_intersect($requiredExamIds, $coveredExamIds);

            if (!empty($intersection)) {
                $totalPrice = 0;
                foreach ($intersection as $examId) {
                    $ae = $lab->availableExams->firstWhere('exam_id', $examId);
                    if ($ae && $ae->price) {
                        $totalPrice += $ae->price;
                    }
                }

                $labCoverage[] = [
                    'labo_id' => $lab->id,
                    'lab_name' => $lab->name,
                    'covered_exams' => array_values($intersection),
                    'covered_count' => count($intersection),
                    'total_price' => $totalPrice,
                ];
            }
        }

        usort($labCoverage, fn($a, $b) => $b['covered_count'] <=> $a['covered_count']);

        return $labCoverage;
    }

    private function buildOptimalSplit(array $labCoverage, array $requiredExamIds): array
    {
        if (empty($labCoverage)) return [];

        $first = $labCoverage[0];
        $remaining = array_diff($requiredExamIds, $first['covered_exams']);

        $split = [
            [
                'labo_id' => $first['labo_id'],
                'lab_name' => $first['lab_name'],
                'exam_ids' => array_values($first['covered_exams']),
                'total_price' => $first['total_price'],
                'is_primary' => true,
            ],
        ];

        if (empty($remaining)) return $split;

        foreach ($labCoverage as $lab) {
            if ($lab['labo_id'] === $first['labo_id']) continue;

            $coveredRemaining = array_intersect($lab['covered_exams'], $remaining);
            if (empty($coveredRemaining)) continue;

            $split[] = [
                'labo_id' => $lab['labo_id'],
                'lab_name' => $lab['lab_name'],
                'exam_ids' => array_values($coveredRemaining),
                'total_price' => $lab['total_price'],
                'is_primary' => false,
            ];

            $remaining = array_diff($remaining, $coveredRemaining);
            if (empty($remaining)) break;
        }

        if (!empty($remaining)) {
            $split[] = [
                'labo_id' => null,
                'lab_name' => 'Non couvert',
                'exam_ids' => array_values($remaining),
                'total_price' => 0,
                'is_primary' => false,
                'uncovered' => true,
            ];
        }

        return $split;
    }
}
