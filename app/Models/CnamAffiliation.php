<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CnamAffiliation extends Model
{
    protected $fillable = ['patient_id', 'cnam_number', 'affiliation_number', 'cnam_rate_id', 'valid_until', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'valid_until' => 'date'];
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function rate()
    {
        return $this->belongsTo(CnamRate::class, 'cnam_rate_id');
    }
}
