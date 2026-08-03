<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * GDPR (RGPD) helpers: portable data export and erasure/anonymisation.
 */
class GdprService
{
    /** Gather every piece of personal data held about the given user. */
    public function export(User $user): array
    {
        $user->load(['patient', 'doctor', 'staff', 'admin']);

        $export = [
            'generated_at' => now()->toIso8601String(),
            'platform' => config('app.name', 'MedixLab'),
            'subject' => [
                'user_id' => $user->id,
                'roles' => $this->rolesOf($user),
            ],
            'account' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'created_at' => optional($user->created_at)->toIso8601String(),
                'last_login_at' => optional($user->last_login_at)->toIso8601String(),
            ],
        ];

        if ($user->patient) {
            $export['patient_profile'] = $this->patientProfile($user);
            $export['medical_records'] = $this->patientMedicalRecords($user);
            $export['billing'] = $this->patientBilling($user);
            $export['samples'] = $this->patientSamples($user);
        }

        if ($user->doctor) {
            $export['doctor_profile'] = $user->doctor->only([
                'id', 'speciality', 'doctor_code', 'is_archive',
            ]);
            $export['prescriptions'] = $this->doctorPrescriptions($user);
            $export['exam_groups'] = $user->doctor->examGroups->map(fn ($g) => $g->only([
                'id', 'name', 'description', 'is_archive',
            ]))->all();
        }

        if ($user->staff) {
            $export['staff_profile'] = $user->staff->only([
                'id', 'staff_code', 'laboratory_id', 'is_archive',
            ]);
        }

        $export['notifications'] = $user->notifications->map(fn ($n) => $n->only([
            'id', 'title', 'message', 'notification_type', 'is_read', 'created_at',
        ]))->all();

        $export['chat_messages'] = ChatMessage::where(fn ($q) => $q->where('sender_id', $user->id)->orWhere('receiver_id', $user->id))
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'direction' => $m->sender_id === $user->id ? 'sent' : 'received',
                'message' => $m->message,
                'created_at' => optional($m->created_at)->toIso8601String(),
            ])
            ->all();

        return $export;
    }

    /**
     * Anonymise (or, with $hard, delete) every personal trace of the user.
     * Clinical records are preserved for the laboratory's legal retention duty.
     */
    public function erase(User $user, bool $hard = false): void
    {
        $user->load(['patient', 'doctor', 'staff']);

        // Wipe personal communication channels.
        Notification::where('user_id', $user->id)->delete();
        ChatMessage::where('sender_id', $user->id)->orWhere('receiver_id', $user->id)->delete();

        if ($hard) {
            // Full erasure: the cascade deletes the profile and clinical records too.
            $user->delete();

            return;
        }

        $user->update([
            'first_name' => 'Anonymisé',
            'last_name' => 'Utilisateur',
            'email' => 'anonyme-'.$user->id.'@medixlab.invalid',
            'phone' => null,
            'address' => null,
            'password' => Hash::make(Str::random(64)),
            'last_login_at' => null,
            'is_archive' => true,
        ]);

        if ($user->patient) {
            $user->patient->update([
                'patient_code' => 'ANON-P-'.$user->id,
                'date_of_birth' => null,
                'gender' => null,
                'blood_group' => null,
                'country' => null,
                'state_code' => null,
                'latitude' => null,
                'longitude' => null,
            ]);
        }

        if ($user->doctor) {
            $user->doctor->update([
                'doctor_code' => 'ANON-D-'.$user->id,
            ]);
        }

        if ($user->staff) {
            $user->staff->update([
                'staff_code' => 'ANON-S-'.$user->id,
            ]);
        }
    }

    /** List the platform roles attached to the user. */
    private function rolesOf(User $user): array
    {
        $roles = [];
        if ($user->patient) {
            $roles[] = 'patient';
        }
        if ($user->doctor) {
            $roles[] = 'doctor';
        }
        if ($user->staff) {
            $roles[] = 'staff';
        }
        if ($user->admin) {
            $roles[] = 'admin';
        }

        return $roles;
    }

    private function patientProfile(User $user): array
    {
        return $user->patient->only([
            'id', 'patient_code', 'blood_group', 'date_of_birth', 'gender',
            'country', 'state_code', 'is_archive',
        ]);
    }

    private function patientMedicalRecords(User $user): array
    {
        return $user->patient->examRequests()
            ->with([
                'doctor.user',
                'laboratory',
                'items.exam',
                'items.resultLabo.details',
            ])
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'status' => $r->status,
                'clinical_notes' => $r->clinical_notes,
                'doctor' => $r->doctor?->user ? ($r->doctor->user->first_name.' '.$r->doctor->user->last_name) : null,
                'laboratory' => $r->laboratory?->name,
                'created_at' => optional($r->created_at)->toIso8601String(),
                'items' => $r->items->map(fn ($item) => [
                    'exam_code' => $item->exam?->code,
                    'exam_name' => $item->exam?->name,
                    'result' => $item->resultLabo ? [
                        'interpretation' => $item->resultLabo->interpretation,
                        'details' => $item->resultLabo->details->map(fn ($d) => $d->only([
                            'parameter', 'value', 'status', 'reference_range', 'unit',
                        ]))->all(),
                    ] : null,
                ])->all(),
            ])
            ->all();
    }

    private function patientBilling(User $user): array
    {
        return $user->patient->invoices()
            ->with('payments')
            ->get()
            ->map(fn ($i) => [
                'id' => $i->id,
                'invoice_number' => $i->invoice_number,
                'total_amount' => $i->total_amount,
                'cnam_amount' => $i->cnam_amount,
                'patient_amount' => $i->patient_amount,
                'paid_amount' => $i->paid_amount,
                'status' => $i->status,
                'created_at' => optional($i->created_at)->toIso8601String(),
                'payments' => $i->payments->map(fn ($p) => [
                    'id' => $p->id,
                    'amount' => $p->amount,
                    'payment_method' => $p->payment_method,
                    'transaction_id' => $p->transaction_id,
                    'payment_date' => optional($p->payment_date)->toIso8601String(),
                ])->all(),
            ])
            ->all();
    }

    private function patientSamples(User $user): array
    {
        return $user->patient->samples()
            ->get()
            ->map(fn ($s) => $s->only([
                'id', 'sample_code', 'status', 'material_type', 'storage_location',
                'collection_date', 'expiry_date', 'rejection_reason',
            ]))
            ->all();
    }

    private function doctorPrescriptions(User $user): array
    {
        return $user->doctor->examRequests()
            ->with(['patient.user', 'items.exam'])
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'status' => $r->status,
                'clinical_notes' => $r->clinical_notes,
                'doctor_interpretation' => $r->doctor_interpretation,
                'patient' => $r->patient?->user ? ($r->patient->user->first_name.' '.$r->patient->user->last_name) : null,
                'created_at' => optional($r->created_at)->toIso8601String(),
                'items' => $r->items->map(fn ($item) => [
                    'exam_code' => $item->exam?->code,
                    'exam_name' => $item->exam?->name,
                ])->all(),
            ])
            ->all();
    }
}
