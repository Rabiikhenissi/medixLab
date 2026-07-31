<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
    protected $fillable = [
        'sample_code', 'exam_request_item_id', 'patient_id', 'labo_id',
        'collected_by', 'material_type', 'status', 'rejection_reason',
        'storage_location', 'collection_date', 'collection_time',
        'expiry_date', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'collection_date' => 'date',
            'collection_time' => 'datetime:H:i',
            'expiry_date' => 'date',
        ];
    }

    public function examRequestItem()
    {
        return $this->belongsTo(ExamRequestItem::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function labo()
    {
        return $this->belongsTo(Labo::class);
    }

    public function collector()
    {
        return $this->belongsTo(Staff::class, 'collected_by');
    }

    public function barcodeLogs()
    {
        return $this->hasMany(SampleBarcodeLog::class);
    }
}
