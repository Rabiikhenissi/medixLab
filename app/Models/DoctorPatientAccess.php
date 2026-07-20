<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Carbon\Carbon;

#[Fillable(['doctor_id', 'patient_id', 'access_status', 'is_archive', 'expires_at'])]
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

    public function scopeNotBlocked($query)
    {
        return $query->where('access_status', '!=', 'blocked');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
