<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['doctor_id', 'patient_id', 'access_status', 'is_archive', 'expires_at'])]
/**
 * Access grant linking a doctor to a patient, with status and expiry.
 */
class DoctorPatientAccess extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    protected $table = 'doctor_patient_access';

    /**
     * Check whether this access grant has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /**
     * Scope: only active (granted + not expired) accesses.
     */
    public function scopeActive($query)
    {
        return $query->where('access_status', 'granted')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', Carbon::now());
            });
    }

    /** Scope: only accesses that are not blocked. */
    public function scopeNotBlocked($query)
    {
        return $query->where('access_status', '!=', 'blocked');
    }

    /** The doctor granted access. */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /** The patient whose data is shared. */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
