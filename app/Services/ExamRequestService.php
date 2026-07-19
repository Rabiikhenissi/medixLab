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
     * Notifies the doctor if everything is done.
     */
    public static function checkCompletion(ExamRequest $examRequest): void
    {
        $examRequest->load('items.resultLabo');
        $allDone = $examRequest->items->every(fn($item) => $item->resultLabo !== null);

        if ($allDone && $examRequest->status !== 'completed') {
            $examRequest->update(['status' => 'completed']);

            if ($examRequest->doctor && $examRequest->doctor->user) {
                $patient = $examRequest->patient->user;
                NotificationService::resultsReady(
                    $examRequest->doctor->user->id,
                    'Toutes les analyses pour ' . $patient->first_name . ' ' . $patient->last_name . ' sont terminées. Vous pouvez maintenant rédiger votre interprétation.',
                    $examRequest->id
                );
            }
        }
    }
}
