<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Sample;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Generic Eloquent observer writing an append-only audit entry for every
 * create/update/delete on sensitive medical models (medico-legal traceability).
 */
class AuditableObserver
{
    private const IGNORED_FIELDS = [
        'password', 'remember_token', 'updated_at', 'last_activity', 'last_login_at',
        'two_factor_code', 'two_factor_code_expires_at',
    ];

    /** Friendly French label for each audited entity. */
    private const ENTITY_LABELS = [
        'User' => 'Compte utilisateur',
        'ExamRequest' => 'Demande d\'analyse',
        'ExamRequestItem' => 'Élément de demande',
        'Sample' => 'Échantillon',
        'ResultLabo' => 'Résultat de laboratoire',
        'ResultLaboDetail' => 'Détail de résultat',
        'MachineConfiguration' => 'Configuration machine',
        'ExamParameter' => 'Paramètre d\'examen',
        'DoctorPatientAccess' => 'Accès médecin-patient',
    ];

    /** French labels for the most common changed fields. */
    private const FIELD_LABELS = [
        'status' => 'statut',
        'value' => 'valeur',
        'interpretation' => 'interprétation',
        'quantity' => 'quantité',
        'min_quantity' => 'seuil minimum',
        'price' => 'prix',
        'total_amount' => 'total',
        'patient_amount' => 'montant patient',
        'paid_amount' => 'montant payé',
        'cnam_amount' => 'part CNAM',
        'first_name' => 'prénom',
        'last_name' => 'nom',
        'email' => 'email',
        'phone' => 'téléphone',
        'address' => 'adresse',
        'blood_group' => 'groupe sanguin',
        'gender' => 'sexe',
        'date_of_birth' => 'date de naissance',
        'sample_code' => 'code échantillon',
        'storage_location' => 'emplacement',
        'collection_date' => 'date de prélèvement',
        'expiry_date' => 'date d\'expiration',
        'rejection_reason' => 'motif de rejet',
        'access_status' => 'statut d\'accès',
        'clinical_notes' => 'notes cliniques',
    ];

    public function created(Model $model): void
    {
        $this->write($model, 'created', 'Création de '.$this->label($model));
    }

    public function updated(Model $model): void
    {
        $changes = $this->diff($model);

        if ($changes === []) {
            return;
        }

        $fields = implode(', ', array_map(fn ($field) => self::FIELD_LABELS[$field] ?? $field, array_keys($changes)));

        $this->write($model, 'updated', 'Modification de '.$this->label($model).' — champs modifiés : '.$fields, $changes);
    }

    public function deleted(Model $model): void
    {
        $this->write($model, 'deleted', 'Suppression de '.$this->label($model));
    }

    public function restored(Model $model): void
    {
        $this->write($model, 'restored', 'Restauration de '.$this->label($model));
    }

    private function label(Model $model): string
    {
        $type = self::ENTITY_LABELS[class_basename($model)] ?? class_basename($model);

        $label = $type.' #'.$model->getKey();
        $hint = $this->nameHint($model);

        return $hint ? $label." ({$hint})" : $label;
    }

    /** Human-readable hint (person name, sample code…) when easily available. */
    private function nameHint(Model $model): string
    {
        return match (true) {
            $model instanceof User => trim(($model->first_name ?? '').' '.($model->last_name ?? '')),
            $model instanceof Sample => (string) ($model->sample_code ?? ''),
            default => '',
        };
    }

    /** Diff of dirty attributes vs their previous values, ignoring sensitive noise. */
    private function diff(Model $model): array
    {
        $original = $model->getOriginal();
        $changes = [];

        foreach ($model->getDirty() as $field => $newValue) {
            if (in_array($field, self::IGNORED_FIELDS, true)) {
                continue;
            }

            $oldValue = $original[$field] ?? null;

            if ($oldValue === $newValue) {
                continue;
            }

            $changes[$field] = ['old' => $oldValue, 'new' => $newValue];
        }

        return $changes;
    }

    private function write(Model $model, string $action, string $description, array $changes = []): void
    {
        try {
            AuditLog::create([
                'user_id' => auth()->id(),
                'role' => auth()->user()?->group?->code,
                'action' => $action,
                'entity_type' => class_basename($model),
                'entity_id' => $model->getKey(),
                'description' => $description,
                'changes' => $changes ?: null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent() ? substr((string) request()->userAgent(), 0, 255) : null,
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
