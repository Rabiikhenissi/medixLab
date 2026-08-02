<?php

namespace App\Services;

use App\Models\AvailableExam;
use App\Models\CnamNomenclature;
use App\Models\Doctor;
use App\Models\DoctorPatientAccess;
use App\Models\ExamRequest;
use App\Models\ExamRequestItem;
use App\Models\Invoice;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;

class ExamRequestService
{
    /**
     * Create an exam request with items and notify the patient.
     *
     * @param  Doctor  $doctor  the prescribing doctor
     * @param  Patient  $patient  the patient receiving the exam request
     * @param  array  $examIds  ids of the exams to include
     * @param  string|null  $clinicalNotes  optional clinical notes from the doctor
     * @return ExamRequest the created exam request
     */
    public static function create(Doctor $doctor, Patient $patient, array $examIds, ?string $clinicalNotes): ExamRequest
    {
        return DB::transaction(function () use ($doctor, $patient, $examIds, $clinicalNotes) {
            // Create the exam request as pending
            $examRequest = ExamRequest::create([
                'doctor_id' => $doctor->id,
                'patient_id' => $patient->id,
                'status' => 'pending',
                'clinical_notes' => $clinicalNotes,
            ]);

            // Attach each requested exam as a request item
            foreach ($examIds as $examId) {
                ExamRequestItem::create([
                    'exam_request_id' => $examRequest->id,
                    'exam_id' => $examId,
                ]);
            }

            // Notify the patient about the new exam request
            NotificationService::examRequest(
                $patient->user_id,
                'Dr. '.$doctor->user->first_name.' '.$doctor->user->last_name.
                    ' vous a prescrit '.count($examIds).' examen(s). Consultez les détails dans vos demandes.',
                $examRequest->id
            );

            // Return the created exam request
            return $examRequest;
        });
    }

    /**
     * Check if all items on a request are completed and update status accordingly.
     * TIER 2.3 — Smart Notifications: detects abnormal results, notifies doctor + patient.
     *
     * @param  ExamRequest  $examRequest  the exam request to check
     */
    public static function checkCompletion(ExamRequest $examRequest): void
    {
        // Load each item with its lab result and result details
        $examRequest->load('items.resultLabo.details');

        // Determine whether every item has a lab result yet
        $allDone = $examRequest->items->every(fn ($item) => $item->resultLabo !== null);

        // Mark the request as completed once all results are in
        if ($allDone && $examRequest->status !== 'completed') {
            $examRequest->update(['status' => 'completed']);

            // Auto-create invoice if exam request has a labo and no invoice exists
            $laboId = $examRequest->labo_id;
            if ($laboId && ! Invoice::where('exam_request_id', $examRequest->id)->exists()) {
                self::autoCreateInvoice($examRequest, $laboId);
            }

            // Collect abnormal result details for the doctor notification
            $abnormalItems = [];
            // Inspect every result detail and flag abnormal values
            foreach ($examRequest->items as $item) {
                if (! $item->resultLabo) {
                    continue;
                }
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

            // Notify the doctor about the completed results
            if ($examRequest->doctor && $examRequest->doctor->user) {
                $patientUser = $examRequest->patient->user;
                $patientName = $patientUser->first_name.' '.$patientUser->last_name;

                // Notify the doctor about the abnormal parameters
                if (! empty($abnormalItems)) {
                    $abnormalSummary = collect($abnormalItems)->take(3)->map(
                        fn ($a) => $a['parameter'].': '.$a['value'].' '.$a['unit'].' ('.$a['status'].')'
                    )->implode(', ');

                    NotificationService::send(
                        $examRequest->doctor->user->id,
                        '⚠ Anomalies détectées — '.$patientName,
                        count($abnormalItems).' paramètre(s) anormal(s) détecté(s) pour '.$patientName.'. '.
                        $abnormalSummary.'. Veuillez rédiger votre interprétation.',
                        'exam_request',
                        $examRequest->id
                    );
                } else {
                    NotificationService::resultsReady(
                        $examRequest->doctor->user->id,
                        'Toutes les analyses pour '.$patientName.' sont terminées. Aucune anomalie détectée. Vous pouvez rédiger votre interprétation.',
                        $examRequest->id
                    );
                }
            }
        }
    }

    /**
     * Auto-create an invoice when exam results are completed.
     *
     * @param  ExamRequest  $examRequest  the completed exam request to bill
     * @param  int  $laboId  the laboratory that will issue the invoice
     */
    protected static function autoCreateInvoice(ExamRequest $examRequest, int $laboId): void
    {
        $examRequest->load('items.exam', 'patient.cnamAffiliation.rate');

        $examCnamMap = [
            1 => '1001', 2 => '1002', 3 => '1003', 4 => '1004', 5 => '1005',
            6 => '1006', 7 => '1007', 8 => '1008', 9 => '1009', 10 => '1010',
        ];
        $cnamNomenclatures = CnamNomenclature::all()->keyBy('code_cnam');

        $cnamTaux = 0;
        $cnamAffiliation = $examRequest->patient->cnamAffiliation ?? null;
        if ($cnamAffiliation && $cnamAffiliation->is_active && $cnamAffiliation->rate) {
            $cnamTaux = $cnamAffiliation->rate->taux;
        }

        $totalAmount = 0;
        $cnamAmount = 0;
        $invoiceItems = [];

        foreach ($examRequest->items as $item) {
            $available = AvailableExam::where('labo_id', $laboId)
                ->where('exam_id', $item->exam_id)
                ->first();
            $price = $available ? $available->price : 0;
            $totalAmount += $price;

            $cnamCode = $examCnamMap[$item->exam_id] ?? null;
            $itemCnamCoverage = 0;
            $valeurB = null;
            if ($cnamCode && $cnamTaux > 0 && isset($cnamNomenclatures[$cnamCode])) {
                $nomen = $cnamNomenclatures[$cnamCode];
                $valeurB = $nomen->valeur_b;
                $itemCnamCoverage = $valeurB * 1 * ($cnamTaux / 100);
                $cnamAmount += $itemCnamCoverage;
            }

            $invoiceItems[] = [
                'exam_id' => $item->exam_id,
                'exam_request_item_id' => $item->id,
                'description' => $item->exam->name,
                'quantity' => 1,
                'unit_price' => $price,
                'total' => $price,
                'cnam_code' => $cnamCode,
                'valeur_b' => $valeurB,
                'cnam_coverage' => $itemCnamCoverage,
            ];
        }

        $patientAmount = max(0, $totalAmount - $cnamAmount);
        $lastInvoice = Invoice::where('labo_id', $laboId)->orderBy('id', 'desc')->first();
        $seq = $lastInvoice ? ((int) substr($lastInvoice->invoice_number, -4)) + 1 : 1;

        DB::transaction(function () use ($examRequest, $laboId, $totalAmount, $cnamAmount, $patientAmount, $invoiceItems, $seq) {
            $invoice = Invoice::create([
                'invoice_number' => 'FAC-'.$laboId.'-'.str_pad($seq, 4, '0', STR_PAD_LEFT),
                'patient_id' => $examRequest->patient_id,
                'labo_id' => $laboId,
                'exam_request_id' => $examRequest->id,
                'status' => 'pending',
                'total_amount' => $totalAmount,
                'cnam_amount' => $cnamAmount,
                'patient_amount' => $patientAmount,
                'paid_amount' => 0,
                'notes' => 'Facture générée automatiquement à la complétion des résultats.',
            ]);

            foreach ($invoiceItems as $iid) {
                $invoice->items()->create($iid);
            }
        });
    }

    /**
     * TIER 2.3 — Check for doctor access approaching expiry and send reminder notifications.
     * Should be called periodically (e.g., via scheduled task).
     */
    public static function checkAccessExpiry(): void
    {
        // Find granted accesses that expire within the next 7 days
        $expiringSoon = DoctorPatientAccess::where('access_status', 'granted')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now()->addDays(7))
            ->where('expires_at', '>', now())
            ->with(['doctor.user', 'patient.user'])
            ->get();

        // Remind both the patient and the doctor before access expires
        foreach ($expiringSoon as $access) {
            $daysLeft = (int) $access->expires_at->diffInDays(now());
            $patientName = $access->patient->user->first_name.' '.$access->patient->user->last_name;
            $doctorName = 'Dr. '.$access->doctor->user->first_name.' '.$access->doctor->user->last_name;

            // Notify the patient that the doctor's access is about to expire
            NotificationService::send(
                $access->patient->user_id,
                'Accès expire dans '.$daysLeft.' jour(s)',
                $doctorName.' a accès à votre dossier médical. Cet accès expire le '.
                $access->expires_at->format('d/m/Y').'. Souhaitez-vous le renouveler ?',
                'access_request',
                $access->id
            );

            // Notify the doctor that their patient access is about to expire
            NotificationService::send(
                $access->doctor->user->id,
                'Accès patient expire bientôt',
                'Votre accès au dossier de '.$patientName.' expire le '.
                $access->expires_at->format('d/m/Y').'. Le patient devra confirmer le renouvellement.',
                'general',
                $access->id
            );
        }
    }
}
