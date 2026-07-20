<?php

namespace App\Services;

use App\Models\ExamRequestItem;
use App\Models\ResultLabo;
use App\Models\ResultLaboDetail;
use App\Models\Staff;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MachineService
{
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('machine.url', 'http://127.0.0.1:5000');
        $this->timeout = config('machine.timeout', 15);
    }

    public function isOnline(): bool
    {
        try {
            $response = Http::timeout($this->timeout)->get($this->baseUrl . '/api/status');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getStatus(): ?array
    {
        try {
            $response = Http::timeout($this->timeout)->get($this->baseUrl . '/api/status');
            return $response->json();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function sendOrder(ExamRequestItem $item): array
    {
        $patient = $item->examRequest->patient;
        $doctor = $item->examRequest->doctor;

        $payload = [
            'order_id' => 'ORD-' . $item->examRequest->id . '-' . $item->id,
            'exam_request_item_id' => $item->id,
            'exam_code' => $item->exam->code,
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->user->first_name . ' ' . $patient->user->last_name,
                'birth_date' => $patient->date_of_birth?->format('Y-m-d') ?? '1990-01-01',
                'sex' => $patient->gender ?? 'M',
            ],
            'doctor' => $doctor ? [
                'id' => $doctor->id,
                'name' => 'Dr. ' . $doctor->user->first_name . ' ' . $doctor->user->last_name,
            ] : null,
        ];

        $response = Http::timeout($this->timeout)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($this->baseUrl . '/api/order', $payload);

        if (!$response->successful()) {
            $body = [];
            try { $body = $response->json(); } catch (\Exception $e) {}
            $errorMsg = $body['error'] ?? 'Réponse inconnue';
            $available = isset($body['available_exams']) ? ' (disponibles: ' . implode(', ', $body['available_exams']) . ')' : '';
            Log::error('Machine order failed', [
                'item_id' => $item->id,
                'exam_code' => $item->exam->code,
                'status' => $response->status(),
                'error' => $errorMsg,
            ]);
            throw new \Exception($errorMsg . $available);
        }

        return $response->json();
    }

    public function processResults(ExamRequestItem $item, array $machineResponse, int $staffId): ResultLabo
    {
        $results = $machineResponse['results'] ?? [];

        $staff = Staff::find($staffId);

        $result = ResultLabo::updateOrCreate(
            ['exam_request_item_id' => $item->id],
            [
                'staff_id' => $staff?->id,
                'interpretation' => 'Résultats générés automatiquement par le BioAnalyzer 3000',
                'is_archive' => false,
            ]
        );

        $result->details()->delete();

        foreach ($results as $r) {
            $statusMap = ['normal' => 'normal', 'high' => 'high', 'low' => 'low'];
            $detailStatus = $statusMap[$r['status']] ?? 'normal';

            ResultLaboDetail::create([
                'result_labo_id' => $result->id,
                'parameter' => $r['parameter'],
                'value' => $r['value'],
                'status' => $detailStatus,
                'reference_range' => $r['reference_range'] ?? null,
                'unit' => $r['unit'] ?? null,
                'is_archive' => false,
            ]);
        }

        ExamRequestService::checkCompletion($item->examRequest);

        return $result;
    }
}
