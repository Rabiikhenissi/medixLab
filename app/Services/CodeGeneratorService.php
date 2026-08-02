<?php

namespace App\Services;

class CodeGeneratorService
{
    /**
     * Generate a unique code for a doctor, e.g. DR-CAR-2508-00012.
     *
     * @param  int  $doctorId  the doctor's id
     * @param  string  $specialty  the doctor's specialty
     * @return string the generated doctor code
     */
    public static function generateDoctorCode(int $doctorId, string $specialty): string
    {
        // Build a 3-letter code from the specialty
        $specialtyCode = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $specialty), 0, 3));
        if (strlen($specialtyCode) < 3) {
            $specialtyCode = str_pad($specialtyCode, 3, 'X');
        }
        // Add the year-month and the zero-padded doctor id
        $yymm = date('ym');
        $paddedId = str_pad((string) $doctorId, 5, '0', STR_PAD_LEFT);

        return "DR-{$specialtyCode}-{$yymm}-{$paddedId}";
    }

    /**
     * Generate a unique code for a patient, e.g. PT-TN-2508-00012.
     *
     * @param  int  $patientId  the patient's id
     * @param  string  $countryCode  the patient's country code
     * @return string the generated patient code
     */
    public static function generatePatientCode(int $patientId, string $countryCode): string
    {
        // Build a 2-letter code from the country
        $country = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $countryCode), 0, 2));
        if (strlen($country) < 2) {
            $country = str_pad($country, 2, 'X');
        }
        // Add the year-month and the zero-padded patient id
        $yymm = date('ym');
        $paddedId = str_pad((string) $patientId, 5, '0', STR_PAD_LEFT);

        return "PT-{$country}-{$yymm}-{$paddedId}";
    }

    /**
     * Generate a unique code for a staff member, e.g. LAB-MDX-2508-00012.
     *
     * @param  int  $staffId  the staff member's id
     * @param  string  $labName  the laboratory name
     * @return string the generated staff code
     */
    public static function generateStaffCode(int $staffId, string $labName): string
    {
        // Build up to 3 initials from the lab name
        $words = explode(' ', preg_replace('/[^a-zA-Z ]/', '', $labName));
        $initials = '';
        foreach ($words as $word) {
            if (! empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }
        $initials = substr($initials, 0, 3);
        if (strlen($initials) < 3) {
            $initials = str_pad($initials, 3, 'X');
        }
        // Add the year-month and the zero-padded staff id
        $yymm = date('ym');
        $paddedId = str_pad((string) $staffId, 5, '0', STR_PAD_LEFT);

        return "LAB-{$initials}-{$yymm}-{$paddedId}";
    }
}
