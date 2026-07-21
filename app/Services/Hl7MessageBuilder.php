<?php

namespace App\Services;

use App\Models\ExamRequestItem;

class Hl7MessageBuilder
{
    private string $timestamp;

    public function __construct()
    {
        $this->timestamp = date('YmdHis');
    }

    public function buildOrmOrder(ExamRequestItem $item): string
    {
        $patient = $item->examRequest->patient;
        $doctor = $item->examRequest->doctor;
        $orderId = 'ORD-' . $item->examRequest->id . '-' . $item->id;
        $patientCode = $patient->patient_code ?? $patient->id;
        $patientName = strtoupper($patient->user->last_name) . '^' . strtoupper($patient->user->first_name);
        $birthDate = $patient->date_of_birth?->format('Ymd') ?? '19900101';
        $sex = $patient->gender ?? 'M';
        $sex = strtoupper($sex) === 'F' ? 'F' : 'M';

        $doctorName = $doctor
            ? strtoupper($doctor->user->last_name) . '^' . strtoupper($doctor->user->first_name)
            : 'UNKNOWN^DOCTOR';

        $msgId = 'MSG' . strtoupper(bin2hex(random_bytes(4)));
        $now = $this->timestamp;

        $segments = [
            $this->buildMsh($msgId, $now),
            $this->buildPid($patientCode, $patientName, $birthDate, $sex),
            $this->buildNk1($patient),
            $this->buildOrc($orderId),
            $this->buildObr($orderId, $item),
            $this->buildZds($item),
        ];

        return implode("\r", $segments) . "\r";
    }

    private function buildMsh(string $msgId, string $timestamp): string
    {
        return implode('|', [
            'MSH',
            '^~\\&',
            'MEDIX_LIS',
            'MEDIX_LAB',
            'ANALYZER',
            'BIOANALYZER',
            $timestamp,
            '',
            'ORM^O01',
            $msgId,
            'P',
            '2.5.1',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ]);
    }

    private function buildPid(string $id, string $name, string $birthDate, string $sex): string
    {
        return implode('|', [
            'PID',
            '1',
            $id . '^MRN^MEDIX',
            $id,
            $name,
            '',
            $birthDate,
            $sex,
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ]);
    }

    private function buildNk1($patient): string
    {
        $address = $patient->user->address ?? '';
        $city = $patient->user->city ?? '';
        $phone = $patient->user->phone ?? '';

        return implode('|', [
            'NK1',
            '1',
            '',
            '',
            '',
            $address,
            $city,
            '',
            '',
            $phone,
        ]);
    }

    private function buildOrc(string $orderId): string
    {
        return implode('|', [
            'ORC',
            'NW',
            $orderId,
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ]);
    }

    private function buildObr(string $orderId, ExamRequestItem $item): string
    {
        $now = $this->timestamp;
        $priority = 'R';

        return implode('|', [
            'OBR',
            '1',
            $orderId,
            $item->exam->code . '^' . $item->exam->name,
            '',
            '',
            $now,
            '',
            '',
            '',
            '',
            '',
            '',
            $now,
            '',
            '',
            '',
            '',
            '',
            $priority,
            '',
            '',
            '',
            '',
            '',
        ]);
    }

    private function buildZds(ExamRequestItem $item): string
    {
        return implode('|', [
            'ZDS',
            $item->exam->code,
            $item->exam->category ?? 'general',
            'BIOANALYZER_3000',
        ]);
    }
}
