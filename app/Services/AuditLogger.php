<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

/**
 * Helper writing append-only audit entries for key application actions
 * (logins, password changes, administrative changes…) that are not covered
 * by the model observers. Confidential content is never stored — only the
 * fact that the action happened, with the technical context.
 */
class AuditLogger
{
    public static function log(
        string $action,
        string $description,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $changes = null,
        ?int $userId = null,
        ?string $role = null,
    ): void {
        try {
            AuditLog::create([
                'user_id' => $userId ?? Auth::id(),
                'role' => $role ?? Auth::user()?->group?->code,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
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
