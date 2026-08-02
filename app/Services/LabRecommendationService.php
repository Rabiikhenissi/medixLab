<?php

namespace App\Services;

use App\Models\AvailableExam;
use App\Models\Labo;
use Carbon\Carbon;

class LabRecommendationService
{
    private float $patientLat;

    private float $patientLng;

    private array $requiredExamIds;

    private array $availabilityMap = [];

    /**
     * Create the lab recommendation service.
     *
     * @param  float|null  $patientLat  patient latitude (0 if unknown)
     * @param  float|null  $patientLng  patient longitude (0 if unknown)
     * @param  array  $requiredExamIds  exam ids the patient needs to perform
     */
    public function __construct(?float $patientLat = null, ?float $patientLng = null, array $requiredExamIds = [])
    {
        $this->patientLat = $patientLat ?? 0;
        $this->patientLng = $patientLng ?? 0;
        $this->requiredExamIds = $requiredExamIds;
    }

    /**
     * Score each laboratory and return them ranked by total score.
     *
     * @param  mixed  $laboratories  collection of labs to rank
     * @return array ranked lab entries with scores and recommendations
     */
    public function rankLabs($laboratories): array
    {
        $ranked = [];
        $this->availabilityMap = [];

        // Score each lab and remember its availability
        foreach ($laboratories as $lab) {
            $scores = $this->computeScores($lab);
            $this->availabilityMap[$lab->id] = $this->getLabAvailability($lab);
            $ranked[] = [
                'lab' => $lab,
                'scores' => $scores,
                'total_score' => $scores['compatibility'] + $scores['price'] + $scores['distance'] + $scores['availability'],
            ];
        }

        // Sort labs by total score, best first
        usort($ranked, fn ($a, $b) => $b['total_score'] <=> $a['total_score']);

        $bestScore = $ranked[0]['total_score'] ?? 100;

        // Attach a recommendation and sort helpers to each ranked lab
        foreach ($ranked as &$entry) {
            $avail = $this->availabilityMap[$entry['lab']->id] ?? ['status' => 'unknown'];
            $entry['recommendation'] = $this->getRecommendation($entry, $bestScore, $avail);
            $entry['sort_price'] = $entry['scores']['total_price'];
            $entry['sort_distance'] = $entry['scores']['distance'];
            $entry['sort_compat'] = $entry['scores']['compatibility'];
        }
        unset($entry);

        return $ranked;
    }

    /**
     * Return the availability map computed during ranking.
     *
     * @return array availability status per lab id
     */
    public function getAvailabilityMap(): array
    {
        return $this->availabilityMap;
    }

    /**
     * Compute the score breakdown of a lab for the current request.
     *
     * @param  Labo  $lab  the lab to score
     * @return array score components and totals
     */
    private function computeScores(Labo $lab): array
    {
        $activeExams = $lab->availableExams->where('is_active', true);
        $labExamIds = $activeExams->pluck('exam_id')->toArray();

        // Count how many required exams this lab can perform
        $coveredCount = 0;
        $totalRequired = count($this->requiredExamIds);

        if ($totalRequired > 0) {
            $coveredCount = count(array_intersect($this->requiredExamIds, $labExamIds));
        }

        // Compatibility contributes up to 40 points, fully compatible if all are covered
        $compatScore = $totalRequired > 0 ? ($coveredCount / $totalRequired) * 40 : 40;
        $isFullyCompatible = $coveredCount === $totalRequired;

        // Sum the price of the required exams available at this lab
        $totalPrice = 0;
        $priceScore = 20;

        if ($totalRequired > 0) {
            $pricesFound = [];
            foreach ($this->requiredExamIds as $examId) {
                $ae = $activeExams->firstWhere('exam_id', $examId);
                if ($ae && $ae->price) {
                    $totalPrice += $ae->price;
                    $pricesFound[] = $ae->price;
                }
            }
            // Reward labs that quote a price for every required exam
            if (count($pricesFound) === $totalRequired) {
                $priceScore = 20;
            } elseif (count($pricesFound) > 0) {
                $priceScore = 10;
            } else {
                $priceScore = 0;
            }
        }

        // Distance contributes up to 20 points based on proximity
        $distanceScore = 10;
        $hasCoords = $lab->latitude && $lab->longitude;
        if ($hasCoords && ($this->patientLat != 0 || $this->patientLng != 0)) {
            $distance = $this->haversine($this->patientLat, $this->patientLng, $lab->latitude, $lab->longitude);
            if ($distance <= 2) {
                $distanceScore = 20;
            } elseif ($distance <= 5) {
                $distanceScore = 16;
            } elseif ($distance <= 10) {
                $distanceScore = 12;
            } elseif ($distance <= 20) {
                $distanceScore = 8;
            } elseif ($distance <= 50) {
                $distanceScore = 4;
            } else {
                $distanceScore = 1;
            }
        }

        // Availability contributes up to 20 points
        $availabilityScore = $this->computeAvailabilityScore($lab);

        return [
            'compatibility' => round($compatScore, 1),
            'price' => $priceScore,
            'total_price' => $totalPrice,
            'distance' => $distanceScore,
            'availability' => $availabilityScore,
            'is_fully_compatible' => $isFullyCompatible,
            'covered_count' => $coveredCount,
        ];
    }

    /**
     * Score how open and reachable the lab is right now.
     *
     * @param  Labo  $lab  the lab to evaluate
     * @return float availability score (0 to 20)
     */
    private function computeAvailabilityScore(Labo $lab): float
    {
        $now = Carbon::now();
        $dayName = $now->englishDayOfWeek;

        // Find today's regular opening hours
        $todayHours = $lab->workingHours
            ->whereNull('date_close')
            ->first(fn ($wh) => strtolower($wh->day) === strtolower($dayName));

        // Check for an exceptional closure today
        $hasException = $lab->workingHours
            ->whereNotNull('date_close')
            ->contains(fn ($wh) => $wh->date_close && $wh->date_close->isToday());

        // Exceptional closure: minimal score
        if ($hasException) {
            return 2;
        }

        // No hours defined or the lab is closed today: minimal score
        if (! $todayHours || $todayHours->is_closed) {
            return 2;
        }

        $openTime = Carbon::parse($todayHours->start_time);
        $closeTime = Carbon::parse($todayHours->end_time);

        // Lab is open now: reward based on remaining hours
        if ($now->between($openTime, $closeTime)) {
            $remainingHours = $now->floatDiffInHours($closeTime);
            if ($remainingHours > 3) {
                return 20;
            } elseif ($remainingHours > 1) {
                return 12;
            } else {
                return 5;
            }
        }

        // Opens later today: reward based on time until opening
        if ($now->lessThan($openTime)) {
            $hoursUntilOpen = $now->floatDiffInHours($openTime);
            if ($hoursUntilOpen <= 2) {
                return 14;
            } elseif ($hoursUntilOpen <= 8) {
                return 10;
            }
        }

        // Outside opening hours with no near future opening
        return 3;
    }

    /**
     * Build a human-readable availability status for a lab right now.
     *
     * @param  Labo  $lab  the lab to inspect
     * @return array status key, label and color
     */
    public function getLabAvailability(Labo $lab): array
    {
        $now = Carbon::now();
        $dayName = $now->englishDayOfWeek;

        // Find today's regular opening hours
        $todayHours = $lab->workingHours
            ->whereNull('date_close')
            ->first(fn ($wh) => strtolower($wh->day) === strtolower($dayName));

        // Check for an exceptional closure today
        $hasException = $lab->workingHours
            ->whereNotNull('date_close')
            ->contains(fn ($wh) => $wh->date_close && $wh->date_close->isToday());

        // Report exceptional closures first
        if ($hasException) {
            return ['status' => 'closed', 'label' => 'Fermé (exception)', 'color' => 'red'];
        }

        // No hours defined for today
        if (! $todayHours) {
            return ['status' => 'unknown', 'label' => 'Horaires non définis', 'color' => 'gray'];
        }

        // Explicitly closed today
        if ($todayHours->is_closed) {
            return ['status' => 'closed', 'label' => 'Fermé aujourd\'hui', 'color' => 'red'];
        }

        $openTime = Carbon::parse($todayHours->start_time);
        $closeTime = Carbon::parse($todayHours->end_time);

        // Lab is open now: distinguish open vs closing soon
        if ($now->between($openTime, $closeTime)) {
            $remainingHours = $now->floatDiffInHours($closeTime);
            if ($remainingHours > 1) {
                return ['status' => 'open', 'label' => 'Ouvert · ferme à '.$closeTime->format('H:i'), 'color' => 'green'];
            } else {
                return ['status' => 'closing_soon', 'label' => 'Ferme bientôt ('.$closeTime->format('H:i').')', 'color' => 'amber'];
            }
        }

        // Opens later today
        if ($now->lessThan($openTime)) {
            return ['status' => 'opens_soon', 'label' => 'Ouvre à '.$openTime->format('H:i'), 'color' => 'amber'];
        }

        // Closed and already past opening time: next opening is tomorrow
        return ['status' => 'closed', 'label' => 'Fermé · ouvre demain '.$openTime->format('H:i'), 'color' => 'red'];
    }

    /**
     * Decide which badges (if any) a ranked lab should display.
     *
     * @param  array  $entry  ranked lab entry
     * @param  float  $bestScore  total score of the best lab
     * @param  array  $availability  availability status array for the lab
     * @return array|null list of badge arrays, or null when no badge applies
     */
    private function getRecommendation(array $entry, float $bestScore, array $availability): ?array
    {
        $scores = $entry['scores'];

        // Relative score compared to the best lab, as a percentage
        $pct = $bestScore > 0 ? ($entry['total_score'] / $bestScore) * 100 : 0;

        // Top tier: full badge list for the best candidates
        if ($pct >= 90) {
            $badges = [];

            // Badge for full coverage or overall best choice
            if ($scores['is_fully_compatible'] && in_array($availability['status'], ['open', 'closing_soon'])) {
                $badges[] = ['text' => 'Meilleur choix', 'color' => 'emerald', 'icon' => 'star'];
            } elseif ($scores['is_fully_compatible']) {
                $badges[] = ['text' => 'Tous examens', 'color' => 'green', 'icon' => 'check'];
            }

            // Badge describing the current opening status
            $availStatus = $availability['status'] ?? 'unknown';
            if ($availStatus === 'open') {
                $badges[] = ['text' => 'Ouvert maintenant', 'color' => 'green', 'icon' => 'clock'];
            } elseif ($availStatus === 'closing_soon') {
                $badges[] = ['text' => $availability['label'], 'color' => 'amber', 'icon' => 'clock'];
            } elseif ($availStatus === 'opens_soon') {
                $badges[] = ['text' => $availability['label'], 'color' => 'amber', 'icon' => 'clock'];
            } elseif ($availStatus === 'closed') {
                $badges[] = ['text' => 'Fermé', 'color' => 'red', 'icon' => 'clock'];
            }

            // Badge for labs that are close to the patient
            if ($scores['distance'] >= 16) {
                $badges[] = ['text' => 'Proche de vous', 'color' => 'indigo', 'icon' => 'map'];
            }

            // Badge showing the total price when known
            if ($scores['total_price'] > 0) {
                $badges[] = ['text' => number_format($scores['total_price'], 2).' TND', 'color' => 'blue', 'icon' => 'price'];
            }

            return $badges ?: null;
        }

        // Second tier: simpler badge set for good candidates
        if ($pct >= 70) {
            $badges = [];

            // Badge for full coverage, otherwise show covered/total exams
            if ($scores['is_fully_compatible']) {
                $badges[] = ['text' => 'Tous examens couverts', 'color' => 'green', 'icon' => 'check'];
            } else {
                $total = count($this->requiredExamIds);
                $badges[] = ['text' => $scores['covered_count'].'/'.$total.' examens', 'color' => 'amber', 'icon' => 'partial'];
            }

            return $badges;
        }

        // Below the threshold: no recommendation
        return null;
    }

    /**
     * Return the cheapest price found for the required exams across all labs.
     *
     * @return float the cheapest price (0 when no price is available)
     */
    public function getCheapestPrice(): float
    {
        $cheapest = PHP_INT_MAX;
        foreach ($this->requiredExamIds as $examId) {
            $minPrice = AvailableExam::where('exam_id', $examId)
                ->where('is_active', true)
                ->whereNotNull('price')
                ->min('price');
            if ($minPrice !== null && $minPrice < $cheapest) {
                $cheapest = $minPrice;
            }
        }

        return $cheapest === PHP_INT_MAX ? 0 : $cheapest;
    }

    /**
     * Compute the great-circle distance between two coordinates in km.
     *
     * @param  float  $lat1  first latitude
     * @param  float  $lng1  first longitude
     * @param  float  $lat2  second latitude
     * @param  float  $lng2  second longitude
     * @return float distance in kilometers
     */
    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
