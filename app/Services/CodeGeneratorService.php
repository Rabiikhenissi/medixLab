<?php

namespace App\Services;

class CodeGeneratorService
{
    public static function generateDoctorCode(int $doctorId, string $specialty): string
    {
        $specialtyCode = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $specialty), 0, 3));
        if (strlen($specialtyCode) < 3) {
            $specialtyCode = str_pad($specialtyCode, 3, 'X');
        }
        $yymm = date('ym');
        $paddedId = str_pad((string)$doctorId, 5, '0', STR_PAD_LEFT);

        return "DR-{$specialtyCode}-{$yymm}-{$paddedId}";
    }

    public static function generatePatientCode(int $patientId, string $countryCode): string
    {
        $country = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $countryCode), 0, 2));
        if (strlen($country) < 2) {
            $country = str_pad($country, 2, 'X');
        }
        $yymm = date('ym');
        $paddedId = str_pad((string)$patientId, 5, '0', STR_PAD_LEFT);

        return "PT-{$country}-{$yymm}-{$paddedId}";
    }

    public static function generateStaffCode(int $staffId, string $labName): string
    {
        $words = explode(' ', preg_replace('/[^a-zA-Z ]/', '', $labName));
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }
        $initials = substr($initials, 0, 3);
        if (strlen($initials) < 3) {
            $initials = str_pad($initials, 3, 'X');
        }
        $yymm = date('ym');
        $paddedId = str_pad((string)$staffId, 5, '0', STR_PAD_LEFT);

        return "LAB-{$initials}-{$yymm}-{$paddedId}";
    }
}
