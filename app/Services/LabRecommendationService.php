<?php

namespace App\Services;

use App\Models\Labo;
use Carbon\Carbon;

class LabRecommendationService
{
    private float $patientLat;
    private float $patientLng;
    private array $requiredExamIds;
    private array $availabilityMap = [];

    public function __construct(?float $patientLat = null, ?float $patientLng = null, array $requiredExamIds = [])
    {
        $this->patientLat = $patientLat ?? 0;
        $this->patientLng = $patientLng ?? 0;
        $this->requiredExamIds = $requiredExamIds;
    }

    public function rankLabs($laboratories): array
    {
        $ranked = [];
        $this->availabilityMap = [];

        foreach ($laboratories as $lab) {
            $scores = $this->computeScores($lab);
            $this->availabilityMap[$lab->id] = $this->getLabAvailability($lab);
            $ranked[] = [
                'lab' => $lab,
                'scores' => $scores,
                'total_score' => $scores['compatibility'] + $scores['price'] + $scores['distance'] + $scores['availability'],
            ];
        }

        usort($ranked, fn($a, $b) => $b['total_score'] <=> $a['total_score']);

        $bestScore = $ranked[0]['total_score'] ?? 100;

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

    public function getAvailabilityMap(): array
    {
        return $this->availabilityMap;
    }

    private function computeScores(Labo $lab): array
    {
        $activeExams = $lab->availableExams->where('is_active', true);
        $labExamIds = $activeExams->pluck('exam_id')->toArray();

        $coveredCount = 0;
        $totalRequired = count($this->requiredExamIds);

        if ($totalRequired > 0) {
            $coveredCount = count(array_intersect($this->requiredExamIds, $labExamIds));
        }

        $compatScore = $totalRequired > 0 ? ($coveredCount / $totalRequired) * 40 : 40;
        $isFullyCompatible = $coveredCount === $totalRequired;

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
            if (count($pricesFound) === $totalRequired) {
                $priceScore = 20;
            } elseif (count($pricesFound) > 0) {
                $priceScore = 10;
            } else {
                $priceScore = 0;
            }
        }

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

    private function computeAvailabilityScore(Labo $lab): float
    {
        $now = Carbon::now();
        $dayName = $now->englishDayOfWeek;

        $todayHours = $lab->workingHours
            ->whereNull('date_close')
            ->first(fn($wh) => strtolower($wh->day) === strtolower($dayName));

        $hasException = $lab->workingHours
            ->whereNotNull('date_close')
            ->contains(fn($wh) => $wh->date_close && $wh->date_close->isToday());

        if ($hasException) {
            return 2;
        }

        if (!$todayHours || $todayHours->is_closed) {
            return 2;
        }

        $openTime = Carbon::parse($todayHours->start_time);
        $closeTime = Carbon::parse($todayHours->end_time);

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

        if ($now->lessThan($openTime)) {
            $hoursUntilOpen = $now->floatDiffInHours($openTime);
            if ($hoursUntilOpen <= 2) {
                return 14;
            } elseif ($hoursUntilOpen <= 8) {
                return 10;
            }
        }

        return 3;
    }

    public function getLabAvailability(Labo $lab): array
    {
        $now = Carbon::now();
        $dayName = $now->englishDayOfWeek;

        $todayHours = $lab->workingHours
            ->whereNull('date_close')
            ->first(fn($wh) => strtolower($wh->day) === strtolower($dayName));

        $hasException = $lab->workingHours
            ->whereNotNull('date_close')
            ->contains(fn($wh) => $wh->date_close && $wh->date_close->isToday());

        if ($hasException) {
            return ['status' => 'closed', 'label' => 'Fermé (exception)', 'color' => 'red'];
        }

        if (!$todayHours) {
            return ['status' => 'unknown', 'label' => 'Horaires non définis', 'color' => 'gray'];
        }

        if ($todayHours->is_closed) {
            return ['status' => 'closed', 'label' => 'Fermé aujourd\'hui', 'color' => 'red'];
        }

        $openTime = Carbon::parse($todayHours->start_time);
        $closeTime = Carbon::parse($todayHours->end_time);

        if ($now->between($openTime, $closeTime)) {
            $remainingHours = $now->floatDiffInHours($closeTime);
            if ($remainingHours > 1) {
                return ['status' => 'open', 'label' => 'Ouvert · ferme à ' . $closeTime->format('H:i'), 'color' => 'green'];
            } else {
                return ['status' => 'closing_soon', 'label' => 'Ferme bientôt (' . $closeTime->format('H:i') . ')', 'color' => 'amber'];
            }
        }

        if ($now->lessThan($openTime)) {
            return ['status' => 'opens_soon', 'label' => 'Ouvre à ' . $openTime->format('H:i'), 'color' => 'amber'];
        }

        return ['status' => 'closed', 'label' => 'Fermé · ouvre demain ' . $openTime->format('H:i'), 'color' => 'red'];
    }

    private function getRecommendation(array $entry, float $bestScore, array $availability): ?array
    {
        $scores = $entry['scores'];
        $pct = $bestScore > 0 ? ($entry['total_score'] / $bestScore) * 100 : 0;

        if ($pct >= 90) {
            $badges = [];

            if ($scores['is_fully_compatible'] && in_array($availability['status'], ['open', 'closing_soon'])) {
                $badges[] = ['text' => 'Meilleur choix', 'color' => 'emerald', 'icon' => 'star'];
            } elseif ($scores['is_fully_compatible']) {
                $badges[] = ['text' => 'Tous examens', 'color' => 'green', 'icon' => 'check'];
            }

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

            if ($scores['distance'] >= 16) {
                $badges[] = ['text' => 'Proche de vous', 'color' => 'indigo', 'icon' => 'map'];
            }

            if ($scores['total_price'] > 0) {
                $badges[] = ['text' => number_format($scores['total_price'], 2) . ' TND', 'color' => 'blue', 'icon' => 'price'];
            }

            return $badges ?: null;
        }

        if ($pct >= 70) {
            $badges = [];

            if ($scores['is_fully_compatible']) {
                $badges[] = ['text' => 'Tous examens couverts', 'color' => 'green', 'icon' => 'check'];
            } else {
                $total = count($this->requiredExamIds);
                $badges[] = ['text' => $scores['covered_count'] . '/' . $total . ' examens', 'color' => 'amber', 'icon' => 'partial'];
            }

            return $badges;
        }

        return null;
    }

    public function getCheapestPrice(): float
    {
        $cheapest = PHP_INT_MAX;
        foreach ($this->requiredExamIds as $examId) {
            $minPrice = \App\Models\AvailableExam::where('exam_id', $examId)
                ->where('is_active', true)
                ->whereNotNull('price')
                ->min('price');
            if ($minPrice !== null && $minPrice < $cheapest) {
                $cheapest = $minPrice;
            }
        }
        return $cheapest === PHP_INT_MAX ? 0 : $cheapest;
    }

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
