<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

/**
 * Generic Eloquent observer writing an append-only audit entry for every
 * create/update/delete on sensitive medical models (medico-legal traceability).
 */
class AuditableObserver
{
    private const IGNORED_FIELDS = [
        'password', 'remember_token', 'updated_at', 'last_activity',
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

        $this->write($model, 'updated', 'Modification de '.$this->label($model), $changes);
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
        return class_basename($model).' #'.$model->getKey();
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
