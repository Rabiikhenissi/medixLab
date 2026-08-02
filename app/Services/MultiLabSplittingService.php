<?php

namespace App\Services;

use App\Models\ExamRequest;
use App\Models\Labo;
use Illuminate\Support\Facades\DB;

class MultiLabSplittingService
{
    private ExamRequest $examRequest;

    /**
     * Create the splitting service for a given exam request.
     *
     * @param  ExamRequest  $examRequest  the exam request to split across labs
     */
    public function __construct(ExamRequest $examRequest)
    {
        $this->examRequest = $examRequest;
    }

    /**
     * Suggest how the request's exams could be split across labs.
     *
     * @return array list of lab assignments (primary lab first, uncovered exams at the end)
     */
    public function getSplitSuggestions(): array
    {
        // Collect the exam ids requested on this exam request
        $requiredExamIds = $this->examRequest->items->pluck('exam_id')->toArray();

        // No exams to split: nothing to suggest
        if (empty($requiredExamIds)) {
            return [];
        }

        // Load all active labs with their available exams
        $labs = Labo::where('is_archive', false)
            ->with(['availableExams' => fn ($q) => $q->where('is_active', true)])
            ->get();

        // Group labs by how many required exams they can cover, then build the split
        $grouped = $this->groupByLabCapability($labs, $requiredExamIds);
        $split = $this->buildOptimalSplit($grouped, $requiredExamIds);

        return $split;
    }

    /**
     * Persist the chosen split by assigning each exam item to its lab.
     *
     * @param  array  $assignments  list of ['labo_id' => int, 'exam_ids' => array]
     * @return bool true on success, false when the transaction fails
     */
    public function assignSplit(array $assignments): bool
    {
        DB::beginTransaction();

        try {
            // Assign each exam item to its chosen lab
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

            // Set the request's default lab and mark it assigned when not already set
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
            // Roll back and report the failure
            DB::rollBack();
            \Log::error('Multi-lab split failed: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Compute how many required exams each lab can cover, ranked by coverage.
     *
     * @param  mixed  $labs  collection of labs
     * @param  array  $requiredExamIds  exam ids that need to be performed
     * @return array lab coverage entries, best first
     */
    private function groupByLabCapability($labs, array $requiredExamIds): array
    {
        $labCoverage = [];

        // Find which required exams each lab can perform
        foreach ($labs as $lab) {
            $coveredExamIds = $lab->availableExams->pluck('exam_id')->toArray();
            $intersection = array_intersect($requiredExamIds, $coveredExamIds);

            // Keep labs that cover at least one required exam
            if (! empty($intersection)) {
                // Sum the price of the covered exams
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

        // Rank labs by number of covered exams, highest first
        usort($labCoverage, fn ($a, $b) => $b['covered_count'] <=> $a['covered_count']);

        return $labCoverage;
    }

    /**
     * Build the optimal split using the lab with the most coverage first.
     *
     * @param  array  $labCoverage  ranked lab coverage entries
     * @param  array  $requiredExamIds  exam ids that need to be performed
     * @return array list of lab assignments, plus an uncovered bucket when needed
     */
    private function buildOptimalSplit(array $labCoverage, array $requiredExamIds): array
    {
        // No labs available: cannot build a split
        if (empty($labCoverage)) {
            return [];
        }

        // Primary lab covers the most required exams
        $first = $labCoverage[0];

        // Compute the exams still not covered after the primary lab
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

        // Single lab already covers everything
        if (empty($remaining)) {
            return $split;
        }

        // Fill the remaining exams with other labs in order of coverage
        foreach ($labCoverage as $lab) {
            if ($lab['labo_id'] === $first['labo_id']) {
                continue;
            }

            $coveredRemaining = array_intersect($lab['covered_exams'], $remaining);
            if (empty($coveredRemaining)) {
                continue;
            }

            $split[] = [
                'labo_id' => $lab['labo_id'],
                'lab_name' => $lab['lab_name'],
                'exam_ids' => array_values($coveredRemaining),
                'total_price' => $lab['total_price'],
                'is_primary' => false,
            ];

            $remaining = array_diff($remaining, $coveredRemaining);
            if (empty($remaining)) {
                break;
            }
        }

        // Mark the exams no lab can perform as uncovered
        if (! empty($remaining)) {
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
