<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
use App\Models\Admin;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\Labo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminGroup = Group::where('code', 'admin')->first();
        $doctorGroup = Group::where('code', 'doctor')->first();
        $patientGroup = Group::where('code', 'patient')->first();
        $centerGroup = Group::where('code', 'center')->first();

        $admin = User::updateOrCreate(
            ['email' => 'admin@medix.com'],
            [
                'first_name' => 'System', 'last_name' => 'Admin',
                'phone' => '12345678', 'password' => Hash::make('password'),
                'group_id' => $adminGroup->id, 'address' => 'Medix Lab HQ', 'is_archive' => false,
            ]
        );
        Admin::updateOrCreate(['user_id' => $admin->id], ['is_archive' => false]);

        $doctorsData = [
            ['first_name' => 'Ahmed', 'last_name' => 'Ben Salah', 'email' => 'doctor@medix.com', 'phone' => '20123456', 'speciality' => 'Medecine Generale', 'lat' => 36.8065, 'lng' => 10.1815],
            ['first_name' => 'Fatma', 'last_name' => 'Trabelsi', 'email' => 'fatma.trabelsi@medix.com', 'phone' => '21345678', 'speciality' => 'Cardiologie', 'lat' => 36.8028, 'lng' => 10.1920],
            ['first_name' => 'Mohamed', 'last_name' => 'Gharbi', 'email' => 'mohamed.gharbi@medix.com', 'phone' => '22456789', 'speciality' => 'Pediatrie', 'lat' => 36.8080, 'lng' => 10.1680],
            ['first_name' => 'Leila', 'last_name' => 'Bouazizi', 'email' => 'leila.bouazizi@medix.com', 'phone' => '23567890', 'speciality' => 'Endocrinologie', 'lat' => 36.8100, 'lng' => 10.1750],
            ['first_name' => 'Youssef', 'last_name' => 'Mansour', 'email' => 'youssef.mansour@medix.com', 'phone' => '24678901', 'speciality' => 'Dermatologie', 'lat' => 36.8040, 'lng' => 10.1850],
        ];

        foreach ($doctorsData as $d) {
            $user = User::updateOrCreate(
                ['email' => $d['email']],
                [
                    'first_name' => $d['first_name'], 'last_name' => $d['last_name'],
                    'phone' => $d['phone'], 'password' => Hash::make('password'),
                    'group_id' => $doctorGroup->id, 'is_archive' => false,
                ]
            );
            Doctor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'speciality' => $d['speciality'],
                    'doctor_code' => 'DOC-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                    'latitude' => $d['lat'], 'longitude' => $d['lng'],
                    'is_archive' => false,
                ]
            );
        }

        $patientsData = [
            ['first_name' => 'Ali', 'last_name' => 'Ben Ali', 'email' => 'patient@medix.com', 'phone' => '50123456', 'dob' => '1990-05-14', 'gender' => 'M', 'country' => 'Tunisia', 'state' => 'Tunis', 'blood' => 'A+'],
            ['first_name' => 'Sarra', 'last_name' => 'Khelifi', 'email' => 'sarra.khelifi@medix.com', 'phone' => '51234567', 'dob' => '1985-11-22', 'gender' => 'F', 'country' => 'Tunisia', 'state' => 'Ariana', 'blood' => 'O+'],
            ['first_name' => 'Karim', 'last_name' => 'Drissi', 'email' => 'karim.drissi@medix.com', 'phone' => '52345678', 'dob' => '1992-03-08', 'gender' => 'M', 'country' => 'Tunisia', 'state' => 'Tunis', 'blood' => 'B+'],
            ['first_name' => 'Nour', 'last_name' => 'Hadj', 'email' => 'nour.hadj@medix.com', 'phone' => '53456789', 'dob' => '1992-07-15', 'gender' => 'F', 'country' => 'Tunisia', 'state' => 'Ben Arous', 'blood' => 'AB+'],
            ['first_name' => 'Omar', 'last_name' => 'Sassi', 'email' => 'omar.sassi@medix.com', 'phone' => '54567890', 'dob' => '1988-01-20', 'gender' => 'M', 'country' => 'Tunisia', 'state' => 'Sfax', 'blood' => 'O-'],
            ['first_name' => 'Amina', 'last_name' => 'Bouchama', 'email' => 'amina.bouchama@medix.com', 'phone' => '55678901', 'dob' => '1995-09-12', 'gender' => 'F', 'country' => 'Tunisia', 'state' => 'Sousse', 'blood' => 'A-'],
            ['first_name' => 'Hassan', 'last_name' => 'Zouari', 'email' => 'hassan.zouari@medix.com', 'phone' => '56789012', 'dob' => '1978-06-30', 'gender' => 'M', 'country' => 'Tunisia', 'state' => 'Nabeul', 'blood' => 'B-'],
            ['first_name' => 'Ines', 'last_name' => 'Ferjani', 'email' => 'ines.ferjani@medix.com', 'phone' => '57890123', 'dob' => '2000-12-01', 'gender' => 'F', 'country' => 'Tunisia', 'state' => 'Monastir', 'blood' => 'O+'],
            ['first_name' => 'Tarek', 'last_name' => 'Maalej', 'email' => 'tarek.maalej@medix.com', 'phone' => '58901234', 'dob' => '1982-04-18', 'gender' => 'M', 'country' => 'Tunisia', 'state' => 'Tunis', 'blood' => 'A+'],
            ['first_name' => 'Rim', 'last_name' => 'Chedly', 'email' => 'rim.chedly@medix.com', 'phone' => '59012345', 'dob' => '1997-08-25', 'gender' => 'F', 'country' => 'Tunisia', 'state' => 'Ariana', 'blood' => 'AB-'],
        ];

        foreach ($patientsData as $p) {
            $user = User::updateOrCreate(
                ['email' => $p['email']],
                [
                    'first_name' => $p['first_name'], 'last_name' => $p['last_name'],
                    'phone' => $p['phone'], 'password' => Hash::make('password'),
                    'group_id' => $patientGroup->id, 'is_archive' => false,
                ]
            );
            Patient::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'patient_code' => 'PAT-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                    'date_of_birth' => $p['dob'], 'gender' => $p['gender'],
                    'country' => $p['country'], 'state_code' => $p['state'],
                    'blood_group' => $p['blood'], 'is_archive' => false,
                ]
            );
        }

        $labs = Labo::where('is_archive', false)->get();
        $staffNames = [
            ['first_name' => 'Nadia', 'last_name' => 'Rejeb', 'email' => 'nadia.rejeb@medix.com', 'phone' => '70123456'],
            ['first_name' => 'Walid', 'last_name' => 'Chaabane', 'email' => 'walid.chaabane@medix.com', 'phone' => '71234567'],
            ['first_name' => 'Amira', 'last_name' => 'Bouznad', 'email' => 'amira.bouznad@medix.com', 'phone' => '72345678'],
        ];

        foreach ($staffNames as $idx => $s) {
            $lab = $labs[$idx] ?? $labs->first();
            if (!$lab) continue;

            $user = User::updateOrCreate(
                ['email' => $s['email']],
                [
                    'first_name' => $s['first_name'], 'last_name' => $s['last_name'],
                    'phone' => $s['phone'], 'password' => Hash::make('password'),
                    'group_id' => $centerGroup->id, 'is_archive' => false,
                ]
            );
            Staff::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'laboratory_id' => $lab->id,
                    'staff_code' => 'STF-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                    'is_archive' => false,
                ]
            );
        }

        $centerUsers = [
            ['first_name' => 'Sami', 'last_name' => 'Tech', 'email' => 'center@medix.com', 'phone' => '80123456', 'lab_idx' => 0],
            ['first_name' => 'Hatem', 'last_name' => 'Admin', 'email' => 'hatem.admin@medix.com', 'phone' => '81234567', 'lab_idx' => 1],
        ];

        foreach ($centerUsers as $cu) {
            $lab = $labs[$cu['lab_idx']] ?? $labs->first();
            if (!$lab) continue;

            $user = User::updateOrCreate(
                ['email' => $cu['email']],
                [
                    'first_name' => $cu['first_name'], 'last_name' => $cu['last_name'],
                    'phone' => $cu['phone'], 'password' => Hash::make('password'),
                    'group_id' => $centerGroup->id, 'is_archive' => false,
                ]
            );
            Staff::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'laboratory_id' => $lab->id,
                    'staff_code' => 'STF-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                    'is_archive' => false,
                ]
            );
        }

        $this->command->info('1 admin, 5 doctors, 10 patients, 5 staff seeded.');
    }
}
