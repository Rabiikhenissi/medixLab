<?php

namespace App\Services;

use App\Models\ExamRequestItem;

class Hl7MessageBuilder
{
    private string $timestamp;

    /**
     * Create a builder that stamps every message with the current timestamp.
     */
    public function __construct()
    {
        $this->timestamp = date('YmdHis');
    }

    /**
     * Build an HL7 ORM^O01 order message for an exam item.
     *
     * @param  ExamRequestItem  $item  the exam item to order
     * @return string the complete HL7 message
     */
    public function buildOrmOrder(ExamRequestItem $item): string
    {
        // Gather patient demographics for the PID segment
        $patient = $item->examRequest->patient;
        $doctor = $item->examRequest->doctor;
        $orderId = 'ORD-'.$item->examRequest->id.'-'.$item->id;
        $patientCode = $patient->patient_code ?? $patient->id;
        $patientName = strtoupper($patient->user->last_name).'^'.strtoupper($patient->user->first_name);
        $birthDate = $patient->date_of_birth?->format('Ymd') ?? '19900101';
        $sex = $patient->gender ?? 'M';
        $sex = strtoupper($sex) === 'F' ? 'F' : 'M';

        // Gather the ordering doctor's name
        $doctorName = $doctor
            ? strtoupper($doctor->user->last_name).'^'.strtoupper($doctor->user->first_name)
            : 'UNKNOWN^DOCTOR';

        // Generate a unique message id for this order
        $msgId = 'MSG'.strtoupper(bin2hex(random_bytes(4)));
        $now = $this->timestamp;

        // Assemble the ORM message segments
        $segments = [
            $this->buildMsh($msgId, $now),
            $this->buildPid($patientCode, $patientName, $birthDate, $sex),
            $this->buildNk1($patient),
            $this->buildOrc($orderId),
            $this->buildObr($orderId, $item),
            $this->buildZds($item),
        ];

        // Join segments with the HL7 segment terminator
        return implode("\r", $segments)."\r";
    }

    /**
     * Build the MSH (message header) segment.
     *
     * @param  string  $msgId  message control id
     * @param  string  $timestamp  message creation time
     * @return string the MSH segment
     */
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

    /**
     * Build the PID (patient identification) segment.
     *
     * @param  string  $id  patient identifier
     * @param  string  $name  patient name in HL7 name format
     * @param  string  $birthDate  patient birth date
     * @param  string  $sex  patient sex
     * @return string the PID segment
     */
    private function buildPid(string $id, string $name, string $birthDate, string $sex): string
    {
        return implode('|', [
            'PID',
            '1',
            $id.'^MRN^MEDIX',
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

    /**
     * Build the NK1 (next of kin) segment with patient contact details.
     *
     * @param  mixed  $patient  the patient model
     * @return string the NK1 segment
     */
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

    /**
     * Build the ORC (common order) segment for a new order.
     *
     * @param  string  $orderId  order identifier
     * @return string the ORC segment
     */
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

    /**
     * Build the OBR (observation request) segment for the ordered exam.
     *
     * @param  string  $orderId  order identifier
     * @param  ExamRequestItem  $item  the exam item being ordered
     * @return string the OBR segment
     */
    private function buildObr(string $orderId, ExamRequestItem $item): string
    {
        $now = $this->timestamp;
        $priority = 'R';

        return implode('|', [
            'OBR',
            '1',
            $orderId,
            $item->exam->code.'^'.$item->exam->name,
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

    /**
     * Build the ZDS (custom Medix) segment with exam metadata.
     *
     * @param  ExamRequestItem  $item  the exam item being ordered
     * @return string the ZDS segment
     */
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
