<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Seed bulk demo/stress data (patient, doctor and staff users, exams, labs and
 * available-exam links) for load testing. Each inserted batch of rows is tracked
 * in storage/app/bulk_seed_tracker.json so it can be fully removed afterwards.
 *
 * Usage:
 *   php artisan stress:seed --patients=500 --doctors=300 --staff=200
 *                           --exams=1000 --labs=1000 --links-per-exam=10
 *
 * Revert everything with: php artisan stress:undo
 */
class SeedStressData extends Command
{
    protected $signature = 'stress:seed
                            {--patients=500 : Number of patient users}
                            {--doctors=300 : Number of doctor users}
                            {--staff=200 : Number of staff users}
                            {--exams=1000 : Number of exams}
                            {--labs=1000 : Number of laboratories}
                            {--links-per-exam=10 : Available-exam links per exam}';

    protected $description = 'Seed bulk stress data (users, exams, labs) and track it for easy undo via stress:undo';

    /**
     * Run the stress seed command.
     *
     * @return int command exit status (SUCCESS on completion)
     */
    public function handle(): int
    {
        $patientCount = (int) $this->option('patients');
        $doctorCount = (int) $this->option('doctors');
        $staffCount = (int) $this->option('staff');
        $examCount = (int) $this->option('exams');
        $labCount = (int) $this->option('labs');
        $linksPerExam = (int) $this->option('links-per-exam');

        $this->warn("Seeding {$patientCount} patients, {$doctorCount} doctors, {$staffCount} staff, {$examCount} exams, {$labCount} labs...");

        // Track the id range inserted per table so stress:undo can delete them
        $ranges = [
            'labos' => [],
            'exams' => [],
            'users' => [],
            'patients' => [],
            'doctors' => [],
            'staff' => [],
            'available_exams' => [],
        ];

        // Insert everything inside a single transaction
        DB::transaction(function () use (
            $patientCount, $doctorCount, $staffCount, $examCount, $labCount, $linksPerExam, &$ranges
        ) {
            $now = now()->format('Y-m-d H:i:s');
            $passwordHash = Hash::make('password');
            $genders = ['M', 'F'];
            $blood = ['A+', 'A-', 'B+', 'O+', 'O-'];
            $specialities = ['Généraliste', 'Cardiologue', 'Pédiatre', 'Endocrinologue', 'Néphrologue'];
            $categories = ['biochemistry', 'hematology', 'microbiology', 'immunology', 'urinalysis', 'other'];

            // 1. Labs
            $labRows = [];
            for ($i = 1; $i <= $labCount; $i++) {
                $labRows[] = [
                    'name' => "Labo Stress {$i}",
                    'address' => "Adresse Stress {$i}",
                    'city' => "Ville {$i}",
                    'phone' => '+216'.str_pad((string) (20000000 + $i), 8, '0', STR_PAD_LEFT),
                    'email' => "stress.lab.{$i}@test.local",
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            $this->insertChunked('labos', $labRows, 200, $ranges['labos']);
            $labIds = $this->chunkIds($ranges['labos']);
            $this->line("  labs: {$labCount}");

            // 2. Exams
            $examRows = [];
            for ($i = 1; $i <= $examCount; $i++) {
                $examRows[] = [
                    'code' => 'STR-'.str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                    'name' => "Examen Stress {$i}",
                    'category' => $categories[($i - 1) % count($categories)],
                    'description' => "Examen de test stress {$i}",
                    'default_normal_range' => '0 - 1',
                    'preparation_instructions' => null,
                    'is_archive' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            $this->insertChunked('exams', $examRows, 200, $ranges['exams']);
            $examIds = $this->chunkIds($ranges['exams']);
            $this->line("  exams: {$examCount}");

            // 3. Users (mixed roles)
            $userRows = [];
            for ($i = 1; $i <= $patientCount; $i++) {
                $userRows[] = $this->userRow("Patient{$i}", "stress.patient.{$i}@test.local", 30000000 + $i, 3, $now, $passwordHash);
            }
            for ($i = 1; $i <= $doctorCount; $i++) {
                $userRows[] = $this->userRow("Docteur{$i}", "stress.doctor.{$i}@test.local", 40000000 + $i, 2, $now, $passwordHash);
            }
            for ($i = 1; $i <= $staffCount; $i++) {
                $userRows[] = $this->userRow("Personnel{$i}", "stress.staff.{$i}@test.local", 50000000 + $i, 4, $now, $passwordHash);
            }
            $this->insertChunked('users', $userRows, 200, $ranges['users']);
            $userIds = $this->chunkIds($ranges['users']);
            $this->line('  users: '.count($userRows));

            // Patients
            $patientRows = [];
            for ($k = 0; $k < $patientCount; $k++) {
                $patientRows[] = [
                    'user_id' => $userIds[$k],
                    'patient_code' => 'PT-STR-'.str_pad((string) ($k + 1), 5, '0', STR_PAD_LEFT),
                    'gender' => $genders[$k % 2],
                    'date_of_birth' => now()->subYears(20 + ($k % 60))->format('Y-m-d'),
                    'country' => 'TN',
                    'state_code' => 'Tunis',
                    'blood_group' => $blood[$k % 5],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            $this->insertChunked('patients', $patientRows, 200, $ranges['patients']);

            // Doctors
            $doctorRows = [];
            for ($k = 0; $k < $doctorCount; $k++) {
                $doctorRows[] = [
                    'user_id' => $userIds[$patientCount + $k],
                    'speciality' => $specialities[$k % 5],
                    'doctor_code' => 'DR-STR-'.str_pad((string) ($k + 1), 5, '0', STR_PAD_LEFT),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            $this->insertChunked('doctors', $doctorRows, 200, $ranges['doctors']);

            // Staff (one per first labs)
            $staffRows = [];
            for ($k = 0; $k < $staffCount; $k++) {
                $staffRows[] = [
                    'user_id' => $userIds[$patientCount + $doctorCount + $k],
                    'laboratory_id' => $labIds[$k],
                    'staff_code' => 'ST-STR-'.str_pad((string) ($k + 1), 5, '0', STR_PAD_LEFT),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            $this->insertChunked('staff', $staffRows, 200, $ranges['staff']);

            // 4. Available exams (each exam linked to ~linksPerExam distinct labs)
            $availableRows = [];
            for ($e = 0; $e < $examCount; $e++) {
                $labStart = ($e * 997) % $labCount;
                for ($l = 0; $l < $linksPerExam; $l++) {
                    $availableRows[] = [
                        'labo_id' => $labIds[($labStart + $l) % $labCount],
                        'exam_id' => $examIds[$e],
                        'price' => round(5 + (($e + $l) % 95), 2),
                        'is_active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
            $this->insertChunked('available_exams', $availableRows, 300, $ranges['available_exams']);
            $this->line('  available_exams: '.count($availableRows));
        });

        // Save the id ranges for the undo command
        $this->storeTracker($ranges);
        $this->info('Stress data seeded. Run `php artisan stress:undo` to revert.');

        return self::SUCCESS;
    }

    /**
     * Build a user row for the bulk insert.
     *
     * @param  string  $name  user display name
     * @param  string  $email  user email
     * @param  int  $phoneSeed  seed used to build a unique phone number
     * @param  int  $groupId  user group id
     * @param  string  $now  shared created/updated timestamp
     * @param  string  $passwordHash  pre-computed password hash
     * @return array user row ready for insertion
     */
    private function userRow(string $name, string $email, int $phoneSeed, int $groupId, string $now, string $passwordHash): array
    {
        return [
            'first_name' => $name,
            'last_name' => 'Stress',
            'email' => $email,
            'password' => $passwordHash,
            'phone' => '+216'.str_pad((string) $phoneSeed, 8, '0', STR_PAD_LEFT),
            'group_id' => $groupId,
            'address' => "Adresse {$name}",
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    /**
     * Insert rows in chunks and record the id range of each chunk.
     *
     * @param  string  $table  table to insert into
     * @param  array  $rows  rows to insert
     * @param  int  $chunkSize  max rows per insert
     * @param  array  $ranges  tracker array receiving the id ranges
     */
    private function insertChunked(string $table, array $rows, int $chunkSize, array &$ranges): void
    {
        $total = count($rows);
        for ($i = 0; $i < $total; $i += $chunkSize) {
            $slice = array_slice($rows, $i, $chunkSize);
            DB::table($table)->insert($slice);
            $first = (int) DB::getPdo()->lastInsertId();
            $last = $first + count($slice) - 1;
            $ranges[] = [$first, $last];
        }
    }

    /**
     * Expand id ranges into a flat list of ids.
     *
     * @param  array  $ranges  list of [first, last] id ranges
     * @return array flat list of ids
     */
    private function chunkIds(array $ranges): array
    {
        $ids = [];
        foreach ($ranges as [$first, $last]) {
            for ($id = $first; $id <= $last; $id++) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    /**
     * Append this seed run's id ranges to the bulk seed tracker file.
     *
     * @param  array  $ranges  id ranges per table
     */
    private function storeTracker(array $ranges): void
    {
        $trackerPath = storage_path('app/bulk_seed_tracker.json');
        $tracker = ['batches' => []];

        if (file_exists($trackerPath)) {
            $existing = json_decode(file_get_contents($trackerPath), true);
            if (is_array($existing) && isset($existing['batches'])) {
                $tracker = $existing;
            }
        }

        $tracker['batches'][] = [
            'seeded_at' => now()->toDateTimeString(),
            'ranges' => $ranges,
        ];

        file_put_contents($trackerPath, json_encode($tracker, JSON_PRETTY_PRINT));
    }
}
