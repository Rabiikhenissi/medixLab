<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\Labo;
use App\Models\Exam;
use App\Models\ExamParameter;
use App\Models\ExamRequest;
use App\Models\ExamRequestItem;
use App\Models\ResultLabo;
use App\Models\ResultLaboDetail;
use App\Models\DoctorPatientAccess;
use App\Models\Notification;
use App\Models\ChatMessage;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedDoctorPatientAccess();
        $this->seedDemoExamRequests();
        $this->seedNotifications();
        $this->seedChatMessages();
    }

    private function seedDoctorPatientAccess(): void
    {
        $doctors = Doctor::pluck('id')->toArray();
        $patients = Patient::pluck('id')->toArray();

        if (empty($doctors) || empty($patients)) return;

        $statuses = ['granted', 'granted', 'granted', 'pending', 'revoked'];

        for ($i = 0; $i < 8; $i++) {
            DoctorPatientAccess::firstOrCreate(
                ['doctor_id' => $doctors[array_rand($doctors)], 'patient_id' => $patients[array_rand($patients)]],
                [
                    'access_status' => $statuses[array_rand($statuses)],
                    'expires_at' => Carbon::now()->addDays(rand(30, 365)),
                    'is_archive' => false,
                ]
            );
        }

        $this->command->info('Doctor-patient access records seeded.');
    }

    private function seedDemoExamRequests(): void
    {
        $labs = Labo::where('is_archive', false)->pluck('id')->toArray();
        $doctors = Doctor::pluck('id')->toArray();
        $patients = Patient::pluck('id')->toArray();
        $staffUsers = Staff::pluck('id')->toArray();
        $allExamIds = Exam::pluck('id')->toArray();

        if (empty($doctors) || empty($patients) || empty($allExamIds)) return;

        $statuses = ['pending', 'assigned', 'collected', 'processing', 'completed'];

        for ($i = 0; $i < 10; $i++) {
            $status = $statuses[array_rand($statuses)];

            $request = ExamRequest::create([
                'doctor_id' => $doctors[array_rand($doctors)],
                'patient_id' => $patients[array_rand($patients)],
                'labo_id' => $status === 'pending' ? null : $labs[array_rand($labs)],
                'status' => $status,
                'clinical_notes' => $this->getRandomClinicalNote(),
                'is_archive' => false,
                'created_at' => Carbon::now()->subDays(rand(0, 14))->subHours(rand(0, 23)),
            ]);
            $request->update(['updated_at' => $request->created_at]);

            $numExams = rand(1, 4);
            $chosen = array_rand(array_flip($allExamIds), min($numExams, count($allExamIds)));
            if (!is_array($chosen)) $chosen = [$chosen];

            foreach ($chosen as $examId) {
                $item = ExamRequestItem::create([
                    'exam_request_id' => $request->id,
                    'exam_id' => $examId,
                    'is_archive' => false,
                ]);

                if (in_array($status, ['processing', 'completed'])) {
                    $result = ResultLabo::create([
                        'exam_request_item_id' => $item->id,
                        'staff_id' => !empty($staffUsers) ? $staffUsers[array_rand($staffUsers)] : null,
                        'interpretation' => $this->getRandomInterpretation(),
                        'is_archive' => false,
                        'created_at' => $request->created_at->addHours(rand(1, 48)),
                    ]);
                    $result->update(['updated_at' => $result->created_at]);

                    $parameters = ExamParameter::where('exam_id', $examId)->get();
                    if ($parameters->isEmpty()) {
                        $parameters = collect([['name' => 'Resultat', 'unit' => '', 'normal_range' => 'Normal']]);
                    }

                    foreach ($parameters as $param) {
                        $normal = $this->parseNormalRange($param->normal_range ?? $param['normal_range'] ?? '');
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
                            'parameter' => $param['name'] ?? $param->name,
                            'value' => (string) $value,
                            'status' => $statusVal,
                            'reference_range' => $param->normal_range ?? $param['normal_range'] ?? '',
                            'unit' => $param->unit ?? $param['unit'] ?? '',
                            'is_archive' => false,
                        ]);
                    }
                }
            }
        }

        $this->command->info('10 demo exam requests with results seeded.');
    }

    private function seedNotifications(): void
    {
        $users = User::where('is_archive', false)->where('group_id', '!=', null)->get();
        if ($users->isEmpty()) return;

        $notifications = [
            ['title' => 'Nouvelle demande d\'acces', 'message' => 'Un medecin a demande l\'acces a votre dossier medical.', 'notification_type' => 'access_request'],
            ['title' => 'Resultats disponibles', 'message' => 'Vos resultats d\'examens sont prets a etre consultes.', 'notification_type' => 'results_ready'],
            ['title' => 'Demande de prescription', 'message' => 'Une nouvelle demande de prescription vous a ete envoyee.', 'notification_type' => 'exam_request'],
            ['title' => 'Alerte de stock', 'message' => 'Un consommable atteint le seuil minimum dans le stock.', 'notification_type' => 'stock_alert'],
            ['title' => 'Rappel de rendez-vous', 'message' => 'Vous avez un rendez-vous prevu demain.', 'notification_type' => 'exam_request'],
            ['title' => 'Acces accorde', 'message' => 'Votre demande d\'acces au dossier patient a ete acceptee.', 'notification_type' => 'access_request'],
        ];

        foreach ($users->random(min(10, $users->count())) as $user) {
            $n = $notifications[array_rand($notifications)];
            Notification::create([
                'user_id' => $user->id, 'title' => $n['title'], 'message' => $n['message'],
                'notification_type' => $n['notification_type'],
                'is_read' => (bool) rand(0, 1), 'is_archive' => false,
                'created_at' => Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 12)),
            ]);
        }

        $this->command->info('Notifications seeded.');
    }

    private function seedChatMessages(): void
    {
        $doctors = Doctor::with('user')->get();
        $patients = Patient::with('user')->get();

        if ($doctors->isEmpty() || $patients->isEmpty()) return;

        $messages = [
            'Bonjour, avez-vous recu mes resultats ?',
            'Oui, tout est disponible. Vous pouvez passer les recuperer.',
            'Merci Docteur. Est-ce que tout est normal ?',
            'Les resultats sont globalement satisfaisants.',
            'D\'accord, je vous remercie.',
            'N\'oubliez pas de prendre vos medicaments regulierement.',
            'Entendu. A quelle prochaine consultation ?',
            'Dans 3 semaines s\'il vous plait.',
            'Parfait, je prends rendez-vous.',
            'Bon courage et bonne sante !',
        ];

        $count = 0;
        for ($i = 0; $i < 10; $i++) {
            $doc = $doctors->random();
            $pat = $patients->random();
            if (!$doc->user || !$pat->user) continue;

            $sender = rand(0, 1) ? $doc->user : $pat->user;
            $receiver = $sender->id === $doc->user->id ? $pat->user : $doc->user;

            ChatMessage::create([
                'sender_id' => $sender->id, 'receiver_id' => $receiver->id,
                'message' => $messages[$count % count($messages)],
                'is_read' => (bool) rand(0, 1), 'is_archive' => false,
                'created_at' => Carbon::now()->subDays(rand(0, 3))->subHours(rand(0, 12)),
            ]);
            $count++;
        }

        $this->command->info($count . ' chat messages seeded.');
    }

    private function parseNormalRange(string $range): array
    {
        $min = 0; $max = 100; $mid = 50;
        $clean = preg_replace('/\s*\(.*?\)/', '', $range);
        $clean = trim(explode('/', $clean)[0]);

        if (preg_match('/([\d.]+)\s*-\s*([\d.]+)/', $clean, $m)) {
            $min = (float) $m[1]; $max = (float) $m[2];
            $mid = round(($min + $max) / 2, 2);
        } elseif (preg_match('/<\s*([\d.]+)/', $clean, $m)) {
            $max = (float) $m[1]; $mid = round($max * 0.6, 2);
        } elseif (preg_match('/>\s*([\d.]+)/', $clean, $m)) {
            $min = (float) $m[1]; $mid = round($min * 1.3, 2);
        }

        return ['min' => $min, 'max' => $max, 'mid' => $mid];
    }

    private function getRandomClinicalNote(): string
    {
        $notes = [
            'Bilan de routine annuel', 'Controle du diabete sucre type 2',
            'Suivi apres traitement antibiotique', 'Bilan pre-operatoire demande',
            'Verification du bilan hepatique', 'Controle du cholesterol',
            'Recherche d\'infection urinaire', 'Suivi thyroidien',
            'Bilan complet demande', 'Verification des carences en fer',
            'Controle de la fonction renale', 'Bilan hormonal prescrit',
            'Recherche de maladie auto-immune', 'Suivi d\'une grossesse',
            'Controle post-hospitalisation',
        ];
        return $notes[array_rand($notes)];
    }

    private function getRandomInterpretation(): string
    {
        $interpretations = [
            'Resultats dans les normes',
            'Legere elevation des transaminases, a recontrole dans 1 mois',
            'Anemie microcytaire suspectee, completer par bilan ferrique',
            'Cholesterol legerement eleve, regime hygieno-dietetique conseille',
            'TSH normale, fonction thyroidienne normale',
            'Glycemie a jeun dans les limites de la normale',
            'Infection urinaire confirmee, traitement a instaurer',
            'CRP elevee, processus inflammatoire en cours',
            'Bilan hepatique normal',
            'Ferritine basse, complementation martiale conseillee',
            'Resultats a interpreter en clinique',
            'Vitamine D insuffisante, supplementation recommandee',
            'PSA dans les normes pour l\'age',
            'Fonction renale normale, DFG > 90 mL/min',
            'RAS, tous les parametres dans les normes',
        ];
        return $interpretations[array_rand($interpretations)];
    }
}
