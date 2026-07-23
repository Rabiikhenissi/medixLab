<?php

namespace App\Services;

use App\Models\ExamRequest;
use App\Models\ExamRequestItem;
use App\Models\DoctorPatientAccess;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;

class ExamRequestService
{
    /**
     * Create an exam request with items and notify the patient.
     */
    public static function create(Doctor $doctor, Patient $patient, array $examIds, ?string $clinicalNotes): ExamRequest
    {
        return DB::transaction(function () use ($doctor, $patient, $examIds, $clinicalNotes) {
            $examRequest = ExamRequest::create([
                'doctor_id'     => $doctor->id,
                'patient_id'    => $patient->id,
                'status'        => 'pending',
                'clinical_notes'=> $clinicalNotes,
            ]);

            foreach ($examIds as $examId) {
                ExamRequestItem::create([
                    'exam_request_id' => $examRequest->id,
                    'exam_id'         => $examId,
                ]);
            }

            NotificationService::examRequest(
                $patient->user_id,
                'Dr. ' . $doctor->user->first_name . ' ' . $doctor->user->last_name .
                    ' vous a prescrit ' . count($examIds) . ' examen(s). Consultez les détails dans vos demandes.',
                $examRequest->id
            );

            return $examRequest;
        });
    }

    /**
     * Check if all items on a request are completed and update status accordingly.
     * TIER 2.3 — Smart Notifications: detects abnormal results, notifies doctor + patient.
     */
    public static function checkCompletion(ExamRequest $examRequest): void
    {
        $examRequest->load('items.resultLabo.details');
        $allDone = $examRequest->items->every(fn($item) => $item->resultLabo !== null);

        if ($allDone && $examRequest->status !== 'completed') {
            $examRequest->update(['status' => 'completed']);

            $abnormalItems = [];
            foreach ($examRequest->items as $item) {
                if (!$item->resultLabo) continue;
                foreach ($item->resultLabo->details as $detail) {
                    if (in_array($detail->status, ['high', 'low', 'abnormal'])) {
                        $abnormalItems[] = [
                            'exam' => $item->exam->name ?? 'Examen',
                            'parameter' => $detail->parameter,
                            'value' => $detail->value,
                            'status' => $detail->status,
                            'unit' => $detail->unit ?? '',
                        ];
                    }
                }
            }

            if ($examRequest->doctor && $examRequest->doctor->user) {
                $patientUser = $examRequest->patient->user;
                $patientName = $patientUser->first_name . ' ' . $patientUser->last_name;

                if (!empty($abnormalItems)) {
                    $abnormalSummary = collect($abnormalItems)->take(3)->map(
                        fn($a) => $a['parameter'] . ': ' . $a['value'] . ' ' . $a['unit'] . ' (' . $a['status'] . ')'
                    )->implode(', ');

                    NotificationService::send(
                        $examRequest->doctor->user->id,
                        '⚠ Anomalies détectées — ' . $patientName,
                        count($abnormalItems) . ' paramètre(s) anormal(s) détecté(s) pour ' . $patientName . '. ' .
                        $abnormalSummary . '. Veuillez rédiger votre interprétation.',
                        'exam_request',
                        $examRequest->id
                    );
                } else {
                    NotificationService::resultsReady(
                        $examRequest->doctor->user->id,
                        'Toutes les analyses pour ' . $patientName . ' sont terminées. Aucune anomalie détectée. Vous pouvez rédiger votre interprétation.',
                        $examRequest->id
                    );
                }
            }
        }
    }

    /**
     * TIER 2.3 — Check for doctor access approaching expiry and send reminder notifications.
     * Should be called periodically (e.g., via scheduled task).
     */
    public static function checkAccessExpiry(): void
    {
        $expiringSoon = DoctorPatientAccess::where('access_status', 'granted')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now()->addDays(7))
            ->where('expires_at', '>', now())
            ->with(['doctor.user', 'patient.user'])
            ->get();

        foreach ($expiringSoon as $access) {
            $daysLeft = (int) $access->expires_at->diffInDays(now());
            $patientName = $access->patient->user->first_name . ' ' . $access->patient->user->last_name;
            $doctorName = 'Dr. ' . $access->doctor->user->first_name . ' ' . $access->doctor->user->last_name;

            NotificationService::send(
                $access->patient->user_id,
                'Accès expire dans ' . $daysLeft . ' jour(s)',
                $doctorName . ' a accès à votre dossier médical. Cet accès expire le ' .
                $access->expires_at->format('d/m/Y') . '. Souhaitez-vous le renouveler ?',
                'access_request',
                $access->id
            );

            NotificationService::send(
                $access->doctor->user->id,
                'Accès patient expire bientôt',
                'Votre accès au dossier de ' . $patientName . ' expire le ' .
                $access->expires_at->format('d/m/Y') . '. Le patient devra confirmer le renouvellement.',
                'general',
                $access->id
            );
        }
    }
}
