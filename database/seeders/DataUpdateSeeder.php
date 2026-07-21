<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Labo;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\User;
use App\Models\ExamParameter;
use App\Models\AvailableExam;
use App\Models\WorkingHours;
use App\Models\ExamRequest;
use App\Models\ExamRequestItem;
use App\Models\ResultLabo;
use App\Models\ResultLaboDetail;
use App\Models\DoctorPatientAccess;
use App\Models\Notification;
use App\Models\ChatMessage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DataUpdateSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedExamParameters();
        $this->seedLab2AvailableExams();
        $this->updatePatients();
        $this->updateDoctors();
        $this->updateLab2Coordinates();
        $this->seedDemoExamRequests();
        $this->seedDoctorPatientAccess();
        $this->seedNotifications();
        $this->seedChatMessages();
    }

    private function seedExamParameters(): void
    {
        $params = [
            // HBA1C (id=1)
            1 => [
                ['name' => 'HbA1c', 'unit' => '%', 'normal_range' => '< 5.7'],
                ['name' => 'Glucose moyen estimé', 'unit' => 'g/L', 'normal_range' => '0.70 - 1.06'],
            ],
            // LIPID (id=33)
            33 => [
                ['name' => 'Cholestérol Total', 'unit' => 'g/L', 'normal_range' => '< 2.00'],
                ['name' => 'HDL Cholestérol', 'unit' => 'g/L', 'normal_range' => '> 0.40'],
                ['name' => 'LDL Cholestérol', 'unit' => 'g/L', 'normal_range' => '< 1.60'],
                ['name' => 'Triglycérides', 'unit' => 'g/L', 'normal_range' => '< 1.50'],
                ['name' => 'VLDL Cholestérol', 'unit' => 'g/L', 'normal_range' => '< 0.45'],
            ],
            // ACUR (id=34)
            34 => [
                ['name' => 'Acide Urique', 'unit' => 'g/L', 'normal_range' => '0.020 - 0.070'],
            ],
            // AMY (id=35)
            35 => [
                ['name' => 'Amylase', 'unit' => 'U/L', 'normal_range' => '30 - 110'],
                ['name' => 'Amylase salivaire', 'unit' => 'U/L', 'normal_range' => '15 - 60'],
            ],
            // BIL (id=36)
            36 => [
                ['name' => 'Bilirubine Totale', 'unit' => 'mg/L', 'normal_range' => '2.0 - 12.0'],
                ['name' => 'Bilirubine Directe', 'unit' => 'mg/L', 'normal_range' => '< 3.0'],
                ['name' => 'Bilirubine Indirecte', 'unit' => 'mg/L', 'normal_range' => '< 10.0'],
            ],
            // TRANS (id=37)
            37 => [
                ['name' => 'ASAT (SGOT)', 'unit' => 'U/L', 'normal_range' => '5 - 40'],
                ['name' => 'ALAT (SGPT)', 'unit' => 'U/L', 'normal_range' => '5 - 41'],
                ['name' => 'PAL', 'unit' => 'U/L', 'normal_range' => '44 - 147'],
                ['name' => 'GGT', 'unit' => 'U/L', 'normal_range' => '7 - 56'],
            ],
            // HEMOC (id=38)
            38 => [
                ['name' => 'Résultat culture', 'unit' => '', 'normal_range' => 'Négatif (pas de croissance)'],
                ['name' => 'Germes identifiés', 'unit' => '', 'normal_range' => 'Non applicable'],
                ['name' => 'Antibiogramme', 'unit' => '', 'normal_range' => 'Non applicable'],
            ],
            // CALCI (id=39)
            39 => [
                ['name' => 'Calcium Total', 'unit' => 'mg/L', 'normal_range' => '85.0 - 105.0'],
                ['name' => 'Calcium Ionisé', 'unit' => 'mmol/L', 'normal_range' => '1.15 - 1.30'],
            ],
            // FER (id=40)
            40 => [
                ['name' => 'Ferritine', 'unit' => 'ng/mL', 'normal_range' => '12 - 300'],
                ['name' => 'Fer Sérique', 'unit' => 'µg/dL', 'normal_range' => '60 - 170'],
                ['name' => 'TIBC', 'unit' => 'µg/dL', 'normal_range' => '250 - 370'],
            ],
            // VITD (id=41)
            41 => [
                ['name' => '25-OH Vitamine D', 'unit' => 'ng/mL', 'normal_range' => '30.0 - 100.0'],
                ['name' => 'Vitamine D (statut)', 'unit' => '', 'normal_range' => 'Optimal: 30-100'],
            ],
            // PSA (id=42)
            42 => [
                ['name' => 'PSA Total', 'unit' => 'ng/mL', 'normal_range' => '< 4.00'],
                ['name' => 'PSA Libre', 'unit' => 'ng/mL', 'normal_range' => '< 0.80'],
                ['name' => 'Ratio PSA Libre/Total', 'unit' => '%', 'normal_range' => '> 25'],
            ],
            // PROT (id=43)
            43 => [
                ['name' => 'Protéines Totales', 'unit' => 'g/L', 'normal_range' => '60.0 - 80.0'],
                ['name' => 'Albumine', 'unit' => 'g/L', 'normal_range' => '35.0 - 50.0'],
                ['name' => 'Alpha-1 Globulines', 'unit' => 'g/L', 'normal_range' => '2.0 - 4.0'],
                ['name' => 'Alpha-2 Globulines', 'unit' => 'g/L', 'normal_range' => '5.0 - 10.0'],
                ['name' => 'Bêta Globulines', 'unit' => 'g/L', 'normal_range' => '6.0 - 12.0'],
                ['name' => 'Gamma Globulines', 'unit' => 'g/L', 'normal_range' => '10.0 - 20.0'],
            ],
            // VITB12 (id=44)
            44 => [
                ['name' => 'Vitamine B12', 'unit' => 'pg/mL', 'normal_range' => '200.0 - 900.0'],
                ['name' => 'Acide folique', 'unit' => 'ng/mL', 'normal_range' => '2.7 - 17.0'],
            ],
            // CLER (id=45)
            45 => [
                ['name' => 'DFG (CKD-EPI)', 'unit' => 'mL/min/1.73m²', 'normal_range' => '> 90'],
                ['name' => 'Créatinine', 'unit' => 'mg/L', 'normal_range' => '6.0 - 12.0'],
            ],
            // ASLO (id=46)
            46 => [
                ['name' => 'ASLO', 'unit' => 'UI/mL', 'normal_range' => '< 200'],
                ['name' => 'Titre ASLO', 'unit' => '', 'normal_range' => 'Négatif (< 200)'],
            ],
            // TPINR (id=47)
            47 => [
                ['name' => 'TP (Taux de Prothrombine)', 'unit' => '%', 'normal_range' => '70.0 - 100.0'],
                ['name' => 'INR', 'unit' => '', 'normal_range' => '0.85 - 1.15'],
            ],
            // TCA (id=48)
            48 => [
                ['name' => 'TCA', 'unit' => 'secondes', 'normal_range' => '30.0 - 40.0'],
                ['name' => 'Rapport TCA/Témoin', 'unit' => '', 'normal_range' => '0.85 - 1.15'],
            ],
            // STREP (id=49)
            49 => [
                ['name' => 'Résultat TDR Streptocoque A', 'unit' => '', 'normal_range' => 'Négatif'],
            ],
            // COPRO (id=50)
            50 => [
                ['name' => 'Coproculture', 'unit' => '', 'normal_range' => 'Flore saprophyte'],
                ['name' => 'Leucocytes fécaux', 'unit' => '/ champ', 'normal_range' => '< 5'],
                ['name' => 'Sang occulte', 'unit' => '', 'normal_range' => 'Négatif'],
            ],
            // FACTR (id=51)
            51 => [
                ['name' => 'Facteur Rhumatoïde', 'unit' => 'UI/mL', 'normal_range' => '< 14.0'],
                ['name' => 'CRP associée', 'unit' => 'mg/L', 'normal_range' => '< 5.0'],
            ],
            // LIPAS (id=52)
            52 => [
                ['name' => 'Lipase', 'unit' => 'U/L', 'normal_range' => '13.0 - 60.0'],
            ],
        ];

        foreach ($params as $examId => $examParams) {
            foreach ($examParams as $p) {
                ExamParameter::updateOrCreate(
                    ['exam_id' => $examId, 'name' => $p['name']],
                    ['unit' => $p['unit'], 'normal_range' => $p['normal_range']]
                );
            }
        }

        $this->command->info('Exam parameters seeded for ' . count($params) . ' exams.');
    }

    private function seedLab2AvailableExams(): void
    {
        $lab = Labo::find(2);
        if (!$lab) return;

        $prices = [
            'CBC' => 120, 'NFS' => 130, 'HBA1C' => 90, 'HB1AC' => 95, 'GLYC' => 25,
            'UREE' => 30, 'CREAT' => 30, 'CRP' => 40, 'VS' => 20, 'IONO' => 55,
            'TSH' => 80, 'ECBU' => 45, 'LIPID' => 75, 'ACUR' => 40, 'AMY' => 65,
            'BIL' => 50, 'TRANS' => 55, 'HEMOC' => 150, 'CALCI' => 40, 'FER' => 70,
            'VITD' => 95, 'PSA' => 85, 'PROT' => 50, 'VITB12' => 90, 'CLER' => 45,
            'ASLO' => 60, 'TPINR' => 35, 'TCA' => 35, 'STREP' => 55, 'COPRO' => 70,
            'FACTR' => 80, 'LIPAS' => 75,
        ];

        foreach ($prices as $code => $price) {
            $exam = Exam::where('code', $code)->first();
            if (!$exam) continue;
            AvailableExam::updateOrCreate(
                ['labo_id' => $lab->id, 'exam_id' => $exam->id],
                ['price' => $price, 'is_active' => true]
            );
        }

        $this->command->info('Clinique Ariana (Lab #2): ' . count($prices) . ' available exams seeded.');
    }

    private function updatePatients(): void
    {
        $patientData = [
            1 => ['date_of_birth' => '1990-05-14', 'gender' => 'M', 'country' => 'Tunisia', 'state_code' => 'Tunis'],
            2 => ['date_of_birth' => '1985-11-22', 'gender' => 'M', 'country' => 'Tunisia', 'state_code' => 'Ariana'],
            3 => ['date_of_birth' => '1992-03-08', 'gender' => 'M', 'country' => 'Tunisia', 'state_code' => 'Tunis'],
            4 => ['date_of_birth' => '1992-07-15', 'gender' => 'F', 'country' => 'Tunisia', 'state_code' => 'Ben Arous'],
        ];

        foreach ($patientData as $id => $data) {
            $patient = Patient::find($id);
            if ($patient) {
                $patient->update($data);
            }
        }

        $this->command->info('Patients updated with profile data.');
    }

    private function updateDoctors(): void
    {
        $doctorData = [
            2 => ['latitude' => 36.8065, 'longitude' => 10.1815],
            3 => ['latitude' => 36.8028, 'longitude' => 10.1920],
            4 => ['latitude' => 36.8080, 'longitude' => 10.1680],
        ];

        foreach ($doctorData as $id => $data) {
            $doc = Doctor::find($id);
            if ($doc) {
                $doc->update($data);
            }
        }

        $this->command->info('Doctors updated with coordinates.');
    }

    private function updateLab2Coordinates(): void
    {
        $lab = Labo::find(2);
        if ($lab && !$lab->latitude) {
            $lab->update(['latitude' => 36.8136, 'longitude' => 10.1786]);
        }
        $this->command->info('Lab #2 coordinates updated.');
    }

    private function seedDemoExamRequests(): void
    {
        if (ExamRequest::count() >= 20) {
            $this->command->info('Enough exam requests exist, skipping.');
            return;
        }

        $labs = Labo::where('is_archive', false)->pluck('id')->toArray();
        $doctors = Doctor::pluck('id')->toArray();
        $patients = Patient::pluck('id')->toArray();
        $staffUsers = Staff::pluck('id')->toArray();
        $allExamIds = Exam::pluck('id')->toArray();

        if (empty($doctors) || empty($patients) || empty($allExamIds)) {
            $this->command->info('Not enough data to seed exam requests.');
            return;
        }

        $statuses = ['pending', 'assigned', 'collected', 'processing', 'completed'];
        $newRequests = [];

        for ($i = 0; $i < 10; $i++) {
            $docId = $doctors[array_rand($doctors)];
            $patId = $patients[array_rand($patients)];
            $labId = $labs[array_rand($labs)];
            $status = $statuses[array_rand($statuses)];

            $request = ExamRequest::create([
                'doctor_id' => $docId,
                'patient_id' => $patId,
                'labo_id' => $status === 'pending' ? null : $labId,
                'status' => $status,
                'clinical_notes' => $this->getRandomClinicalNote(),
                'created_at' => Carbon::now()->subDays(rand(0, 14))->subHours(rand(0, 23)),
            ]);
            $request->update(['updated_at' => $request->created_at]);

            $numExams = rand(1, 4);
            $chosenExams = array_rand(array_flip($allExamIds), min($numExams, count($allExamIds)));
            if (!is_array($chosenExams)) $chosenExams = [$chosenExams];

            foreach ($chosenExams as $examId) {
                $item = ExamRequestItem::create([
                    'exam_request_id' => $request->id,
                    'exam_id' => $examId,
                ]);

                if (in_array($status, ['processing', 'completed'])) {
                    $staffId = !empty($staffUsers) ? $staffUsers[array_rand($staffUsers)] : null;
                    $result = ResultLabo::create([
                        'exam_request_item_id' => $item->id,
                        'staff_id' => $staffId,
                        'interpretation' => $this->getRandomInterpretation(),
                        'created_at' => $request->created_at->addHours(rand(1, 48)),
                    ]);
                    $result->update(['updated_at' => $result->created_at]);

                    $exam = Exam::find($examId);
                    if ($exam) {
                        $parameters = ExamParameter::where('exam_id', $examId)->get();
                        if ($parameters->isEmpty()) {
                            $parameters = collect([
                                ['name' => 'Résultat', 'unit' => '', 'normal_range' => 'Normal'],
                            ]);
                        }
                        foreach ($parameters as $param) {
                            $normal = $this->parseNormalRange($param->normal_range ?? '');
                            $statusVal = 'normal';
                            $value = $normal['mid'];

                            if (rand(1, 10) <= 2) {
                                $statusVal = ['high', 'low', 'abnormal'][array_rand(['high', 'low', 'abnormal'])];
                                $value = $statusVal === 'high'
                                    ? round($normal['max'] * (1 + rand(10, 40) / 100), 2)
                                    : ($statusVal === 'low'
                                        ? round($normal['min'] * (1 - rand(10, 40) / 100), 2)
                                        : 'Anormal');
                            }

                            ResultLaboDetail::create([
                                'result_labo_id' => $result->id,
                                'parameter' => $param['name'],
                                'value' => (string) $value,
                                'status' => $statusVal,
                                'reference_range' => $param->normal_range ?? $param['normal_range'] ?? '',
                                'unit' => $param->unit ?? $param['unit'] ?? '',
                            ]);
                        }
                    }
                }
            }

            $newRequests[] = $request;
        }

        $this->command->info(count($newRequests) . ' demo exam requests with results seeded.');
    }

    private function seedDoctorPatientAccess(): void
    {
        $doctors = Doctor::pluck('id')->toArray();
        $patients = Patient::pluck('id')->toArray();

        if (empty($doctors) || empty($patients)) return;

        $statuses = ['granted', 'granted', 'pending', 'revoked'];
        $count = 0;

        for ($i = 0; $i < 8; $i++) {
            $docId = $doctors[array_rand($doctors)];
            $patId = $patients[array_rand($patients)];

            $access = DoctorPatientAccess::firstOrCreate(
                ['doctor_id' => $docId, 'patient_id' => $patId],
                [
                    'access_status' => $statuses[array_rand($statuses)],
                    'expires_at' => Carbon::now()->addDays(rand(30, 365)),
                ]
            );
            $count++;
        }

        $this->command->info($count . ' doctor-patient access records seeded.');
    }

    private function seedNotifications(): void
    {
        $users = User::where('is_archive', false)->where('group_id', '!=', null)->get();
        if ($users->isEmpty()) return;

        $types = ['access_request', 'exam_request', 'results_ready', 'stock_alert'];
        $notifications = [
            ['title' => 'Nouvelle demande d\'accès', 'message' => 'Un médecin a demandé l\'accès à votre dossier médical.', 'notification_type' => 'access_request'],
            ['title' => 'Résultats disponibles', 'message' => 'Vos résultats d\'examens sont prêts à être consultés.', 'notification_type' => 'results_ready'],
            ['title' => 'Demande de prescription', 'message' => 'Une nouvelle demande de prescription vous a été envoyée.', 'notification_type' => 'exam_request'],
            ['title' => 'Alerte de stock', 'message' => 'Un consommable atteint le seuil minimum dans le stock.', 'notification_type' => 'stock_alert'],
            ['title' => 'Rappel de rendez-vous', 'message' => 'Vous avez un rendez-vous prévu demain.', 'notification_type' => 'exam_request'],
            ['title' => 'Accès accordé', 'message' => 'Votre demande d\'accès au dossier patient a été acceptée.', 'notification_type' => 'access_request'],
        ];

        $count = 0;
        foreach ($users->random(min(10, $users->count())) as $user) {
            $n = $notifications[array_rand($notifications)];
            Notification::create([
                'user_id' => $user->id,
                'title' => $n['title'],
                'message' => $n['message'],
                'notification_type' => $n['notification_type'],
                'is_read' => (bool) rand(0, 1),
                'created_at' => Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 12)),
            ]);
            $count++;
        }

        $this->command->info($count . ' notifications seeded.');
    }

    private function seedChatMessages(): void
    {
        if (ChatMessage::count() >= 10) {
            $this->command->info('Enough chat messages exist, skipping.');
            return;
        }

        $doctors = Doctor::with('user')->get();
        $patients = Patient::with('user')->get();

        if ($doctors->isEmpty() || $patients->isEmpty()) return;

        $messages = [
            'Bonjour, avez-vous reçu mes résultats ?',
            'Oui, tout est disponible. Vous pouvez passer les récupérer.',
            'Merci Docteur. Est-ce que tout est normal ?',
            'Les résultats sont globalement satisfaisants. Je vous enverrai une ordonnance si nécessaire.',
            'D\'accord, je vous remercie.',
            'N\'oubliez pas de prendre vos médicaments régulièrement.',
            'Entendu. À quelle prochaine consultation ?',
            'Dans 3 semaines s\'il vous plaît.',
            'Parfait, je prends rendez-vous.',
            'Bon courage et bonne santé !',
        ];

        $count = 0;
        for ($i = 0; $i < 10; $i++) {
            $doc = $doctors->random();
            $pat = $patients->random();

            if (!$doc->user || !$pat->user) continue;

            $sender = rand(0, 1) ? $doc->user : $pat->user;
            $receiver = $sender->id === $doc->user->id ? $pat->user : $doc->user;

            ChatMessage::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'message' => $messages[$count % count($messages)],
                'is_read' => (bool) rand(0, 1),
                'created_at' => Carbon::now()->subDays(rand(0, 3))->subHours(rand(0, 12)),
            ]);
            $count++;
        }

        $this->command->info($count . ' chat messages seeded.');
    }

    private function parseNormalRange(string $range): array
    {
        $min = 0;
        $max = 100;
        $mid = 50;

        $cleanRange = preg_replace('/\s*\(.*?\)/', '', $range);
        $cleanRange = explode('/', $cleanRange)[0];
        $cleanRange = trim($cleanRange);

        if (preg_match('/([\d.]+)\s*-\s*([\d.]+)/', $cleanRange, $m)) {
            $min = (float) $m[1];
            $max = (float) $m[2];
            $mid = round(($min + $max) / 2, 2);
        } elseif (preg_match('/<\s*([\d.]+)/', $cleanRange, $m)) {
            $max = (float) $m[1];
            $mid = round($max * 0.6, 2);
        } elseif (preg_match('/>\s*([\d.]+)/', $cleanRange, $m)) {
            $min = (float) $m[1];
            $mid = round($min * 1.3, 2);
        }

        return ['min' => $min, 'max' => $max, 'mid' => $mid];
    }

    private function getRandomClinicalNote(): string
    {
        $notes = [
            'Bilan de routine annuel',
            'Contrôle du diabète sucré type 2',
            'Suivi après traitement antibiotique',
            'Bilan pré-opératoire demandé',
            'Vérification du bilan hépatique',
            'Contrôle du cholestérol',
            'Recherche d\'infection urinaire',
            'Suivi thyroïdien',
            'Bilan complet demandé',
            'Vérification des carences en fer',
            'Contrôle de la fonction rénale',
            'Bilan hormonal prescrit',
            'Recherche de maladie auto-immune',
            'Suivi d\'une grossesse',
            'Contrôle post-hospitalisation',
        ];

        return $notes[array_rand($notes)];
    }

    private function getRandomInterpretation(): string
    {
        $interpretations = [
            'Résultats dans les normes',
            'Légère élévation des transaminases, à recontrôler dans 1 mois',
            'Anémie microcytaire suspectée, compléter par bilan ferrique',
            'Cholestérol légèrement élevé, régime hygiéno-diétique conseillé',
            'TSH normale, fonction thyroïdienne normale',
            'Glycémie à jeun dans les limites de la normale',
            'Infection urinaire confirmée, traitement antibiotique à instaurer',
            'CRP élevée, processus inflammatoire en cours',
            'Bilan hépatique normal',
            'Ferritine basse, complémentation martiale conseillée',
            'Résultats à interpréter en clinique',
            'Vitamine D insuffisante, supplémentation recommandée',
            'PSA dans les normes pour l\'âge',
            'Fonction rénale normale, DFG > 90 mL/min',
            'RAS, tous les paramètres dans les normes',
        ];

        return $interpretations[array_rand($interpretations)];
    }
}
