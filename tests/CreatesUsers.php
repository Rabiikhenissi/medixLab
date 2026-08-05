<?php

namespace Tests;

use App\Models\Doctor;
use App\Models\Exam;
use App\Models\Labo;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Str;

trait CreatesUsers
{
    protected function makeUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'email_verified_at' => now(),
        ], $overrides));
    }

    protected function makePatient(): array
    {
        $user = $this->makeUser();
        $patient = Patient::create([
            'user_id' => $user->id,
            'patient_code' => 'PT-TST-'.Str::upper(Str::random(6)),
        ]);

        return ['user' => $user, 'patient' => $patient];
    }

    protected function makeDoctor(): array
    {
        $user = $this->makeUser();
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'speciality' => 'Généraliste',
            'doctor_code' => 'DR-TST-'.Str::upper(Str::random(6)),
        ]);

        return ['user' => $user, 'doctor' => $doctor];
    }

    protected function makeStaff(Labo $labo): array
    {
        $user = $this->makeUser();
        $staff = Staff::create([
            'user_id' => $user->id,
            'laboratory_id' => $labo->id,
            'staff_code' => 'ST-TST-'.Str::upper(Str::random(6)),
        ]);

        return ['user' => $user, 'staff' => $staff];
    }

    protected function makeLabo(string $name = 'Labo Test'): Labo
    {
        return Labo::create([
            'name' => $name,
            'city' => 'Tunis',
        ]);
    }

    protected function makeExam(): Exam
    {
        return Exam::create([
            'code' => 'EX-'.Str::upper(Str::random(6)),
            'name' => 'Glycémie',
            'category' => 'biochemistry',
        ]);
    }
}
