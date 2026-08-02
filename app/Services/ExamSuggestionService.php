<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamRequestItem;
use App\Models\Patient;

class ExamSuggestionService
{
    private Patient $patient;

    private static $AGE_GROUP_MAP = [
        'child' => ['min' => 0, 'max' => 17],
        'adult' => ['min' => 18, 'max' => 39],
        'middle' => ['min' => 40, 'max' => 59],
        'senior' => ['min' => 60, 'max' => 120],
    ];

    private static $GENDER_AGNOSTIC = ['NFS', 'BIOCH', 'HEME'];

    /**
     * Create the suggestion service for a given patient.
     *
     * @param  Patient  $patient  the patient to generate suggestions for
     */
    public function __construct(Patient $patient)
    {
        $this->patient = $patient;
    }

    /**
     * Build exam suggestions for the patient (preventive, follow-up, age-based).
     *
     * @param  array|null  $alreadySelectedExamIds  exam ids already picked by the doctor
     * @return array sorted list of suggestion arrays (max 10)
     */
    public function getSuggestions(?array $alreadySelectedExamIds = []): array
    {
        // Normalize the list of exams the doctor already selected
        $alreadySelectedExamIds = $alreadySelectedExamIds ?? [];
        $suggestions = [];
        $age = $this->getAgeGroup();
        $gender = $this->patient->gender;

        // Load patient history to detect abnormalities and recent exams
        $pastResults = $this->getPatientResultHistory();
        $abnormalParams = $this->getAbnormalParameters($pastResults);
        $recentExams = $this->getRecentExamCodes();

        // Merge preventive, follow-up and age-based suggestions
        $preventive = $this->getPreventiveSuggestions($age, $gender, $recentExams);
        $followUp = $this->getFollowUpSuggestions($abnormalParams);
        $ageBased = $this->getAgeBasedSuggestions($age);

        $allSuggestions = array_merge($preventive, $followUp, $ageBased);

        // Drop exams already selected by the doctor or already suggested
        $seen = [];
        foreach ($allSuggestions as $s) {
            if (in_array($s['exam_id'], $alreadySelectedExamIds)) {
                continue;
            }
            if (in_array($s['exam_id'], $seen)) {
                continue;
            }
            $seen[] = $s['exam_id'];
            $suggestions[] = $s;
        }

        // Sort by priority, highest first
        usort($suggestions, fn ($a, $b) => $b['priority'] <=> $a['priority']);

        // Keep only the top 10 suggestions
        return array_slice($suggestions, 0, 10);
    }

    /**
     * Assign human-readable category tags to an exam based on its code and name.
     *
     * @param  Exam  $exam  the exam to tag
     * @return array list of unique category tags
     */
    public static function getExamCategoryTags(Exam $exam): array
    {
        $tags = [];
        $code = strtoupper($exam->code);
        $name = strtolower($exam->name);

        // Derive tags from the exam code
        if (in_array($code, ['NFS', 'VS', 'HEME', 'COAG'])) {
            $tags[] = 'hematologie';
        }
        if (in_array($code, ['BIOCH', 'GLUC', 'CHOL', 'TRIG', 'UREA', 'CREAT', 'ALAT', 'ASAT', 'BILI'])) {
            $tags[] = 'biochimie';
        }
        if (in_array($code, ['URIN', 'ECBU'])) {
            $tags[] = 'urin分析';
        }
        if (in_array($code, ['TSH', 'FT4', 'FT3', 'INS', 'CORT'])) {
            $tags[] = 'endocrinologie';
        }
        if (in_array($code, ['HB1AC', 'FRUC'])) {
            $tags[] = 'diabète';
        }
        if (in_array($code, ['IRON', 'FERR', 'VITB12', 'FOL'])) {
            $tags[] = 'carence';
        }
        if (in_array($code, ['PSA'])) {
            $tags[] = 'prostate';
        }
        if (in_array($code, ['LIPID'])) {
            $tags[] = 'cardiovasculaire';
        }
        if (in_array($code, ['CALCI', 'PHOSP', 'MAGN'])) {
            $tags[] = 'électrolytes';
        }
        if (in_array($code, ['AMY', 'LIPAS'])) {
            $tags[] = 'pancréas';
        }

        // Derive extra tags from the exam name
        if (str_contains($name, 'foie') || str_contains($name, 'hépat')) {
            $tags[] = 'foie';
        }
        if (str_contains($name, 'rein') || str_contains($name, 'rénal')) {
            $tags[] = 'rein';
        }
        if (str_contains($name, 'thyroid') || str_contains($name, 'thyro')) {
            $tags[] = 'thyroïde';
        }
        if (str_contains($name, 'glyc') || str_contains($name, 'sang')) {
            $tags[] = 'diabète';
        }
        if (str_contains($name, 'cholest') || str_contains($name, 'lipid')) {
            $tags[] = 'cardiovasculaire';
        }
        if (str_contains($name, 'vitamin') || str_contains($name, 'fer')) {
            $tags[] = 'carence';
        }

        return array_unique($tags);
    }

    private function getAgeGroup(): string
    {
        $age = $this->patient->date_of_birth
            ? $this->patient->date_of_birth->age
            : 30;

        foreach (self::$AGE_GROUP_MAP as $group => $range) {
            if ($age >= $range['min'] && $age <= $range['max']) {
                return $group;
            }
        }

        return 'adult';
    }

    private function getPreventiveSuggestions(string $ageGroup, ?string $gender, array $recentExams): array
    {
        $suggestions = [];

        $preventiveMap = [
            'child' => [
                ['code' => 'NFS', 'reason' => 'Bilan hématologique de routine (enfant)', 'priority' => 70],
                ['code' => 'BIOCH', 'reason' => 'Bilan biochimique de base', 'priority' => 65],
                ['code' => 'URIN', 'reason' => 'Analyse urinaire de routine', 'priority' => 60],
            ],
            'adult' => [
                ['code' => 'NFS', 'reason' => 'Bilan hématologique annuel recommandé', 'priority' => 80],
                ['code' => 'BIOCH', 'reason' => 'Bilan biochimique annuel', 'priority' => 75],
                ['code' => 'CHOL', 'reason' => 'Dosage du cholestérol (recommandé tous les 5 ans)', 'priority' => 70],
                ['code' => 'GLUC', 'reason' => 'Glycémie à jeun (dépistage diabète)', 'priority' => 72],
                ['code' => 'TSH', 'reason' => 'Dépistage thyroïdien (recommandé)', 'priority' => 60],
            ],
            'middle' => [
                ['code' => 'NFS', 'reason' => 'Bilan hématologique semestriel', 'priority' => 85],
                ['code' => 'BIOCH', 'reason' => 'Bilan biochimique complet', 'priority' => 82],
                ['code' => 'CHOL', 'reason' => 'Profil lipidique (risque cardiovasculaire)', 'priority' => 80],
                ['code' => 'GLUC', 'reason' => 'Glycémie + HbA1c (dépistage diabète)', 'priority' => 78],
                ['code' => 'HB1AC', 'reason' => 'Hémoglobine glyquée (contrôle glycémique)', 'priority' => 76],
                ['code' => 'TSH', 'reason' => 'Fonction thyroïdienne', 'priority' => 70],
                ['code' => 'CREAT', 'reason' => 'Fonction rénale (créatinine)', 'priority' => 68],
                ['code' => 'ALAT', 'reason' => 'Fonction hépatique', 'priority' => 65],
            ],
            'senior' => [
                ['code' => 'NFS', 'reason' => 'Bilan hématologique trimestriel', 'priority' => 90],
                ['code' => 'BIOCH', 'reason' => 'Bilan biochimique complet', 'priority' => 88],
                ['code' => 'CHOL', 'reason' => 'Profil lipidique complet', 'priority' => 85],
                ['code' => 'GLUC', 'reason' => 'Glycémie + HbA1c', 'priority' => 84],
                ['code' => 'HB1AC', 'reason' => 'Hémoglobine glyquée (surveillance diabète)', 'priority' => 82],
                ['code' => 'TSH', 'reason' => 'Fonction thyroïdienne', 'priority' => 78],
                ['code' => 'CREAT', 'reason' => 'Fonction rénale', 'priority' => 80],
                ['code' => 'ALAT', 'reason' => 'Fonction hépatique', 'priority' => 75],
                ['code' => 'VITD', 'reason' => 'Dosage vitamine D (prévalence carence)', 'priority' => 70],
                ['code' => 'CALCI', 'reason' => 'Calcium sérique (risque ostéoporose)', 'priority' => 68],
            ],
        ];

        if ($gender === 'male') {
            $preventiveMap['middle'][] = ['code' => 'PSA', 'reason' => 'Dépistage prostate (hommes 50+)', 'priority' => 72];
            $preventiveMap['senior'][] = ['code' => 'PSA', 'reason' => 'Dépistage prostate (hommes 50+)', 'priority' => 80];
        }

        $map = $preventiveMap[$ageGroup] ?? $preventiveMap['adult'];

        foreach ($map as $item) {
            if (in_array(strtoupper($item['code']), $recentExams)) {
                continue;
            }
            $exam = Exam::where('code', $item['code'])->where('is_archive', false)->first();
            if (! $exam) {
                continue;
            }
            $suggestions[] = [
                'exam_id' => $exam->id,
                'exam_name' => $exam->name,
                'exam_code' => $exam->code,
                'category' => $exam->category,
                'reason' => $item['reason'],
                'type' => 'preventive',
                'priority' => $item['priority'],
            ];
        }

        return $suggestions;
    }

    private function getFollowUpSuggestions(array $abnormalParams): array
    {
        $suggestions = [];
        $followUpMap = [
            'Hb' => [['code' => 'NFS', 'reason' => 'Suivi anémie — NFS de contrôle', 'priority' => 90]],
            'HbA1c' => [['code' => 'HB1AC', 'reason' => 'Suivi diabète — HbA1c de contrôle', 'priority' => 95]],
            'Glucose' => [['code' => 'GLUC', 'reason' => 'Glycémie pertinente — contrôle recommandé', 'priority' => 88]],
            'Cholesterol Total' => [['code' => 'CHOL', 'reason' => 'Cholestérol élevé — profil lipidique recommandé', 'priority' => 87]],
            'LDL' => [['code' => 'LIPID', 'reason' => 'LDL élevé — profil lipidique complet', 'priority' => 86]],
            'TSH' => [['code' => 'TSH', 'reason' => 'TSH anormale — contrôle thyroïdien recommandé', 'priority' => 92]],
            'Creatinine' => [['code' => 'CREAT', 'reason' => 'Créatinine anormale — fonction rénale à surveiller', 'priority' => 89]],
            'ALT' => [['code' => 'ALAT', 'reason' => 'ALAT élevée — fonction hépatique à surveiller', 'priority' => 88]],
            'AST' => [['code' => 'ASAT', 'reason' => 'ASAT élevée — bilan hépatique recommandé', 'priority' => 87]],
            'Ferritine' => [['code' => 'FERR', 'reason' => 'Ferritine anormale — bilan fer recommandé', 'priority' => 85]],
            'Vitamine D' => [['code' => 'VITD', 'reason' => 'Vitamine D basse — supplémentation et contrôle', 'priority' => 80]],
        ];

        foreach ($abnormalParams as $param => $details) {
            $mapping = $followUpMap[$param] ?? null;
            if (! $mapping) {
                continue;
            }

            foreach ($mapping as $item) {
                $exam = Exam::where('code', $item['code'])->where('is_archive', false)->first();
                if (! $exam) {
                    continue;
                }
                $suggestions[] = [
                    'exam_id' => $exam->id,
                    'exam_name' => $exam->name,
                    'exam_code' => $exam->code,
                    'category' => $exam->category,
                    'reason' => $item['reason'].' (valeur: '.$details['value'].' '.($details['unit'] ?? '').')',
                    'type' => 'follow_up',
                    'priority' => $item['priority'],
                ];
            }
        }

        return $suggestions;
    }

    private function getAgeBasedSuggestions(string $ageGroup): array
    {
        $suggestions = [];
        $map = [
            'senior' => [
                ['code' => 'PSA', 'reason' => 'Dépistage prostate recommandé après 60 ans', 'priority' => 65],
            ],
        ];

        $items = $map[$ageGroup] ?? [];
        foreach ($items as $item) {
            $exam = Exam::where('code', $item['code'])->where('is_archive', false)->first();
            if (! $exam) {
                continue;
            }
            $suggestions[] = [
                'exam_id' => $exam->id,
                'exam_name' => $exam->name,
                'exam_code' => $exam->code,
                'category' => $exam->category,
                'reason' => $item['reason'],
                'type' => 'age_based',
                'priority' => $item['priority'],
            ];
        }

        return $suggestions;
    }

    private function getPatientResultHistory(): array
    {
        $items = ExamRequestItem::whereHas('examRequest', fn ($q) => $q
            ->where('patient_id', $this->patient->id)
            ->where('status', 'completed')
        )
            ->whereHas('resultLabo')
            ->with('resultLabo.details')
            ->get();

        $history = [];
        foreach ($items as $item) {
            if ($item->resultLabo && $item->resultLabo->details) {
                foreach ($item->resultLabo->details as $detail) {
                    $history[] = [
                        'parameter' => $detail->parameter,
                        'value' => $detail->value,
                        'status' => $detail->status,
                        'unit' => $detail->unit,
                        'created_at' => $item->created_at,
                    ];
                }
            }
        }

        return $history;
    }

    private function getAbnormalParameters(array $history): array
    {
        $abnormal = [];
        $recentThreshold = now()->subMonths(6);

        foreach ($history as $entry) {
            if ($entry['created_at'] < $recentThreshold) {
                continue;
            }
            if (in_array($entry['status'], ['high', 'low', 'abnormal'])) {
                $param = $entry['parameter'];
                if (! isset($abnormal[$param]) || $entry['created_at'] > $abnormal[$param]['created_at']) {
                    $abnormal[$param] = $entry;
                }
            }
        }

        return $abnormal;
    }

    private function getRecentExamCodes(): array
    {
        $cutoff = now()->subMonths(3);

        return ExamRequestItem::whereHas('examRequest', fn ($q) => $q
            ->where('patient_id', $this->patient->id)
            ->where('created_at', '>=', $cutoff)
        )
            ->with('exam')
            ->get()
            ->pluck('exam.code')
            ->filter()
            ->map(fn ($c) => strtoupper($c))
            ->unique()
            ->values()
            ->toArray();
    }
}
