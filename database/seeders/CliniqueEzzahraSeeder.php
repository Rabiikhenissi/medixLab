<?php

namespace Database\Seeders;

use App\Models\AvailableExam;
use App\Models\CnamAffiliation;
use App\Models\CnamNomenclature;
use App\Models\CnamRate;
use App\Models\Exam;
use App\Models\ExamRequest;
use App\Models\ExamRequestItem;
use App\Models\Invoice;
use App\Models\MachineConfiguration;
use App\Models\Sample;
use App\Models\SampleBarcodeLog;
use Illuminate\Database\Seeder;

class CliniqueEzzahraSeeder extends Seeder
{
    public function run(): void
    {
        $laboId = 21;
        $patientId = 11;
        $staffId = 6;

        // ============================================================
        // 1. CNAM NOMENCLATURES
        // ============================================================
        $cnamData = [
            ['code_cnam' => '1001', 'exam_name' => 'Hémogramme (NFS)', 'valeur_b' => 15.000, 'taux' => 75],
            ['code_cnam' => '1002', 'exam_name' => 'Glycémie à jeun', 'valeur_b' => 5.000, 'taux' => 75],
            ['code_cnam' => '1003', 'exam_name' => 'HbA1c', 'valeur_b' => 12.000, 'taux' => 75],
            ['code_cnam' => '1004', 'exam_name' => 'Urée sanguine', 'valeur_b' => 4.000, 'taux' => 75],
            ['code_cnam' => '1005', 'exam_name' => 'Créatinine sanguine', 'valeur_b' => 4.000, 'taux' => 75],
            ['code_cnam' => '1006', 'exam_name' => 'CRP', 'valeur_b' => 6.000, 'taux' => 60],
            ['code_cnam' => '1007', 'exam_name' => 'VS', 'valeur_b' => 5.000, 'taux' => 60],
            ['code_cnam' => '1008', 'exam_name' => 'Ionogramme sanguin', 'valeur_b' => 10.000, 'taux' => 75],
            ['code_cnam' => '1009', 'exam_name' => 'TSH', 'valeur_b' => 14.000, 'taux' => 75],
            ['code_cnam' => '1010', 'exam_name' => 'ECBU', 'valeur_b' => 8.000, 'taux' => 60],
            ['code_cnam' => '1011', 'exam_name' => 'Bilan lipidique', 'valeur_b' => 12.000, 'taux' => 75],
            ['code_cnam' => '1012', 'exam_name' => 'Fer sérique', 'valeur_b' => 7.000, 'taux' => 60],
            ['code_cnam' => '1013', 'exam_name' => 'Vitamine D', 'valeur_b' => 25.000, 'taux' => 50],
            ['code_cnam' => '1014', 'exam_name' => 'Cortisol', 'valeur_b' => 20.000, 'taux' => 50],
            ['code_cnam' => '1015', 'exam_name' => 'Test de grossesse (β-hCG)', 'valeur_b' => 10.000, 'taux' => 60],
        ];

        foreach ($cnamData as $d) {
            CnamNomenclature::updateOrCreate(
                ['code_cnam' => $d['code_cnam']],
                [
                    'exam_name' => $d['exam_name'],
                    'valeur_b' => $d['valeur_b'],
                    'taux' => $d['taux'],
                    'description' => null,
                    'is_active' => true,
                ]
            );
        }
        $this->command->info('Created '.count($cnamData).' CNAM nomenclatures');

        // ============================================================
        // 2. CNAM RATES
        // ============================================================
        $rates = [
            ['code' => 'assure_social', 'label' => 'Assuré social', 'taux' => 100],
            ['code' => 'conjoint', 'label' => 'Conjoint', 'taux' => 50],
            ['code' => 'enfant', 'label' => 'Enfant', 'taux' => 25],
        ];
        foreach ($rates as $r) {
            CnamRate::updateOrCreate(
                ['code' => $r['code']],
                ['label' => $r['label'], 'taux' => $r['taux'], 'is_active' => true]
            );
        }
        $this->command->info('Created '.count($rates).' CNAM rates');

        // ============================================================
        // 3. CNAM AFFILIATION for patient 11
        // ============================================================
        $rateId = CnamRate::where('code', 'assure_social')->value('id');
        CnamAffiliation::updateOrCreate(
            ['patient_id' => $patientId],
            [
                'cnam_number' => 'CNAM-'.str_pad($patientId, 8, '0', STR_PAD_LEFT),
                'affiliation_number' => 'AFF-'.str_pad($patientId, 8, '0', STR_PAD_LEFT),
                'cnam_rate_id' => $rateId,
                'valid_until' => now()->addYear(),
                'is_active' => true,
            ]
        );
        $this->command->info('Created CNAM affiliation for patient '.$patientId);

        // ============================================================
        // 4. AVAILABLE EXAMS for Clinique Ezzahra
        // ============================================================
        $examPrices = [
            1 => 12, 2 => 5, 3 => 18, 4 => 6, 5 => 6,
            6 => 8, 7 => 5, 8 => 15, 9 => 18, 10 => 10,
            11 => 20, 12 => 6, 13 => 8, 14 => 7, 15 => 10,
            16 => 15, 17 => 7, 18 => 12, 19 => 25, 20 => 22,
            21 => 5, 22 => 20, 23 => 8, 24 => 10, 25 => 8,
            26 => 8, 27 => 7, 28 => 12, 29 => 10, 30 => 8,
        ];

        foreach ($examPrices as $examId => $price) {
            AvailableExam::updateOrCreate(
                ['labo_id' => $laboId, 'exam_id' => $examId],
                ['price' => $price, 'is_active' => true, 'is_archive' => false]
            );
        }
        $this->command->info('Created '.count($examPrices).' available exams');

        // ============================================================
        // 5. INVOICES from completed exam requests (with CNAM)
        // ============================================================
        $examCnamMap = [
            1 => '1001', 2 => '1002', 3 => '1003', 4 => '1004', 5 => '1005',
            6 => '1006', 7 => '1007', 8 => '1008', 9 => '1009', 10 => '1010',
        ];
        $cnamRateId = CnamRate::where('code', 'assure_social')->value('id');
        $cnamTaux = CnamRate::where('code', 'assure_social')->value('taux');
        $cnamNomenclatures = CnamNomenclature::all()->keyBy('code_cnam');

        $completedIds = [11, 12, 13, 14];
        $itemsGrouped = ExamRequestItem::whereIn('exam_request_id', $completedIds)
            ->with('exam')
            ->get()
            ->groupBy('exam_request_id');

        foreach ($completedIds as $erId) {
            $er = ExamRequest::find($erId);
            if (! $er) {
                continue;
            }

            $items = $itemsGrouped->get($erId, collect());
            if ($items->isEmpty()) {
                continue;
            }

            $totalAmount = 0;
            $cnamAmount = 0;
            $invoiceItems = [];
            foreach ($items as $item) {
                $available = AvailableExam::where('labo_id', $laboId)
                    ->where('exam_id', $item->exam_id)
                    ->first();
                $price = $available ? $available->price : 0;
                $totalAmount += $price;

                $cnamCode = $examCnamMap[$item->exam_id] ?? null;
                $itemCnamCoverage = 0;
                $valeurB = null;
                if ($cnamCode && isset($cnamNomenclatures[$cnamCode])) {
                    $nomen = $cnamNomenclatures[$cnamCode];
                    $valeurB = $nomen->valeur_b;
                    $itemCnamCoverage = $valeurB * 1 * ($cnamTaux / 100);
                    $cnamAmount += $itemCnamCoverage;
                }

                $invoiceItems[] = [
                    'exam_id' => $item->exam_id,
                    'exam_request_item_id' => $item->id,
                    'description' => $item->exam->name,
                    'quantity' => 1,
                    'unit_price' => $price,
                    'total' => $price,
                    'cnam_code' => $cnamCode,
                    'valeur_b' => $valeurB,
                    'cnam_coverage' => $itemCnamCoverage,
                ];
            }

            $patientAmount = max(0, $totalAmount - $cnamAmount);

            $invoice = Invoice::create([
                'invoice_number' => 'FAC-'.$laboId.'-'.str_pad($erId, 4, '0', STR_PAD_LEFT),
                'patient_id' => $patientId,
                'labo_id' => $laboId,
                'exam_request_id' => $erId,
                'status' => 'pending',
                'total_amount' => $totalAmount,
                'cnam_amount' => $cnamAmount,
                'patient_amount' => $patientAmount,
                'paid_amount' => 0,
                'notes' => 'Facture générée depuis la demande #'.$erId,
            ]);

            foreach ($invoiceItems as $iid) {
                $invoice->items()->create($iid);
            }

            $this->command->info("Created invoice [{$invoice->id}] {$invoice->invoice_number} = {$totalAmount} TND (CNAM: {$cnamAmount}, Patient: {$patientAmount})");
        }

        // ============================================================
        // 6. SAMPLES
        // ============================================================
        $materialTypes = ['blood', 'urine', 'serum', 'plasma'];
        $created = 0;
        foreach ($itemsGrouped as $erId => $items) {
            foreach ($items as $item) {
                if ($created >= 6) {
                    break 2;
                }

                $material = $materialTypes[array_rand($materialTypes)];
                $sampleCode = 'SMP-'.$laboId.'-'.str_pad($item->id, 5, '0', STR_PAD_LEFT);

                $sample = Sample::create([
                    'sample_code' => $sampleCode,
                    'exam_request_item_id' => $item->id,
                    'patient_id' => $patientId,
                    'labo_id' => $laboId,
                    'collected_by' => $staffId,
                    'material_type' => $material,
                    'status' => 'collected',
                    'collection_date' => now()->subHours(rand(1, 48))->format('Y-m-d'),
                    'collection_time' => now()->subHours(rand(1, 48))->format('H:i'),
                    'notes' => 'Échantillon prélevé',
                ]);

                SampleBarcodeLog::create([
                    'sample_id' => $sample->id,
                    'action' => 'created',
                    'staff_id' => $staffId,
                    'notes' => 'Échantillon créé avec code '.$sampleCode,
                ]);

                $this->command->info("Created sample [{$sample->id}] {$sampleCode} ({$material})");
                $created++;
            }
        }

        // ============================================================
        // 5. LAB MACHINE CONFIGURATIONS (LIS connections)
        // ============================================================
        $machines = [
            [
                'name' => 'Finecare FIA Meter Plus (Wondfo)',
                'host' => '127.0.0.1',
                'port' => 5011,
                'mllp_port' => 5011,
                'timeout' => 15,
                'enabled' => false,
            ],
            [
                'name' => 'Wondfo Finecare Mini',
                'host' => '127.0.0.1',
                'port' => 5012,
                'mllp_port' => 5012,
                'timeout' => 15,
                'enabled' => false,
            ],
            [
                'name' => 'Zybio Z3 (Hématologie 3 part.)',
                'host' => '127.0.0.1',
                'port' => 5013,
                'mllp_port' => 5013,
                'timeout' => 15,
                'enabled' => false,
            ],
            [
                'name' => 'ELITechGroup Selectra ProS (Biochimie)',
                'host' => '127.0.0.1',
                'port' => 5014,
                'mllp_port' => 5014,
                'timeout' => 15,
                'enabled' => false,
            ],
            [
                'name' => 'Mindray BA-88A (Biochimie semi-auto)',
                'host' => '127.0.0.1',
                'port' => 5015,
                'mllp_port' => 5015,
                'timeout' => 15,
                'enabled' => false,
            ],
            [
                'name' => 'Boditech iCHROMA II (FIA)',
                'host' => '127.0.0.1',
                'port' => 5016,
                'mllp_port' => 5016,
                'timeout' => 15,
                'enabled' => false,
            ],
        ];

        $machineCount = 0;
        foreach ($machines as $machine) {
            MachineConfiguration::updateOrCreate(
                ['labo_id' => $laboId, 'name' => $machine['name']],
                array_merge([
                    'protocol' => 'hl7_mllp',
                    'api_key' => null,
                    'enabled' => false,
                    'is_archive' => false,
                ], $machine)
            );
            $machineCount++;
        }

        $this->command->info("Created {$machineCount} machine configurations (all disabled until connected)");

        $this->command->info('Seeding complete!');
    }
}
