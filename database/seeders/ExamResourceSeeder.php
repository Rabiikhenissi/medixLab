<?php

namespace Database\Seeders;

use App\Models\AvailableExam;
use App\Models\Consumable;
use App\Models\Equipment;
use App\Models\Exam;
use App\Models\ExamConsumable;
use App\Models\ExamEquipment;
use App\Models\Labo;
use Illuminate\Database\Seeder;

class ExamResourceSeeder extends Seeder
{
    public function run(): void
    {
        $labs = Labo::where('is_archive', false)->get();
        $exams = Exam::where('is_archive', false)->get();

        if ($labs->isEmpty() || $exams->isEmpty()) {
            $this->command->info('No labs or exams found, skipping available exams.');

            return;
        }

        $basePrices = [
            'NFS' => 130, 'GLYC' => 25, 'HB1AC' => 95, 'UREE' => 30, 'CREAT' => 30,
            'CRP' => 40, 'VS' => 20, 'IONO' => 55, 'TSH' => 80, 'ECBU' => 45,
            'LIPID' => 75, 'ACUR' => 40, 'AMY' => 65, 'BIL' => 50, 'TRANS' => 55,
            'HEMOC' => 150, 'CALCI' => 40, 'FER' => 70, 'VITD' => 95, 'PSA' => 85,
            'PROT' => 50, 'VITB12' => 90, 'CLER' => 45, 'ASLO' => 60, 'TPINR' => 35,
            'TCA' => 35, 'STREP' => 55, 'COPRO' => 70, 'FACTR' => 80, 'LIPAS' => 75,
        ];

        foreach ($labs as $lab) {
            foreach ($exams as $exam) {
                $base = $basePrices[$exam->code] ?? rand(20, 80);
                AvailableExam::updateOrCreate(
                    ['labo_id' => $lab->id, 'exam_id' => $exam->id],
                    [
                        'price' => $base + (rand(0, 30)),
                        'is_active' => true, 'is_archive' => false,
                    ]
                );
            }
        }

        $this->command->info($labs->count().' labs x '.$exams->count().' exams = available exams seeded.');

        $examEquipmentMap = [
            'NFS' => 'Analyseur d\'Hematologie Laser 5 Populations',
            'GLYC' => 'Analyseur de Biochimie Clinique Automatique',
            'HB1AC' => 'Analyseur de Biochimie Clinique Automatique',
            'UREE' => 'Analyseur de Biochimie Clinique Automatique',
            'CREAT' => 'Analyseur de Biochimie Clinique Automatique',
            'CRP' => 'Kit reactif liquide de dosage quantitatif de la CRP',
            'VS' => 'Microscope optique trinoculaire LED de recherche',
            'IONO' => 'Analyseur de Biochimie Clinique Automatique',
            'TSH' => 'Automate d\'Immunologie Multiparametrique ELISA',
            'ECBU' => 'Analyseur d\'Urine Automatise par Reflectometrie',
            'LIPID' => 'Analyseur de Biochimie Clinique Automatique',
            'TRANS' => 'Analyseur de Biochimie Clinique Automatique',
            'HEMOC' => 'Incubateur bacteriologique thermostaté a 37C',
            'TPINR' => 'Automate de Coagulation et d\'Hemostase rapide',
            'TCA' => 'Automate de Coagulation et d\'Hemostase rapide',
            'STREP' => 'Microscope optique trinoculaire LED de recherche',
        ];

        $examConsumableMap = [
            'NFS' => ['Tubes EDTA pour hematologie (bouchon violet)'],
            'GLYC' => ['Tubes Secs avec activateur de coagulation (bouchon rouge)'],
            'HB1AC' => ['Tubes Secs avec activateur de coagulation (bouchon rouge)'],
            'UREE' => ['Tubes Secs avec activateur de coagulation (bouchon rouge)'],
            'CREAT' => ['Tubes Secs avec activateur de coagulation (bouchon rouge)'],
            'CRP' => ['Kit reactif liquide de dosage quantitatif de la CRP'],
            'VS' => ['Tubes EDTA pour hematologie (bouchon violet)'],
            'IONO' => ['Tubes Heparine pour biochimie (bouchon vert)'],
            'TSH' => ['Tubes Secs avec activateur de coagulation (bouchon rouge)'],
            'ECBU' => ["Flacons de recueil d'urine steriles graduates (60 mL)"],
            'LIPID' => ['Tubes Secs avec activateur de coagulation (bouchon rouge)', 'Embouts de pipettes jetables neutres (100-1000 uL)'],
            'TRANS' => ['Tubes Secs avec activateur de coagulation (bouchon rouge)'],
            'HEMOC' => ["Flacons de recueil d'urine steriles graduates (60 mL)"],
            'TPINR' => ['Tubes Citrate pour coagulation (bouchon bleu)'],
            'TCA' => ['Tubes Citrate pour coagulation (bouchon bleu)'],
            'STREP' => ['Ecouvillons nasopharynges steriles avec milieu de transport'],
            'COPRO' => ["Flacons de recueil d'urine steriles graduates (60 mL)"],
        ];

        foreach ($labs as $lab) {
            $labConsumables = Consumable::where('labo_id', $lab->id)->get()->keyBy('name');
            $labEquipment = Equipment::where('labo_id', $lab->id)->get()->keyBy('name');

            foreach ($exams as $exam) {
                if (isset($examEquipmentMap[$exam->code])) {
                    $eqName = $examEquipmentMap[$exam->code];
                    if (isset($labEquipment[$eqName])) {
                        ExamEquipment::updateOrCreate(
                            ['exam_id' => $exam->id, 'equipment_id' => $labEquipment[$eqName]->id],
                            ['is_archive' => false]
                        );
                    }
                }

                if (isset($examConsumableMap[$exam->code])) {
                    foreach ($examConsumableMap[$exam->code] as $cName) {
                        if (isset($labConsumables[$cName])) {
                            ExamConsumable::updateOrCreate(
                                ['exam_id' => $exam->id, 'consumable_id' => $labConsumables[$cName]->id],
                                ['quantity_needed' => 1, 'is_archive' => false]
                            );
                        }
                    }
                }
            }
        }

        $this->command->info('Exam-consumable and exam-equipment links seeded.');
    }
}
