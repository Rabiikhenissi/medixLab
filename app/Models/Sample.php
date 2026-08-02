<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Biological sample collected for a lab result, tracked by barcode through its lifecycle.
 */
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

    /** The exam request item this sample was collected for. */
    public function examRequestItem()
    {
        return $this->belongsTo(ExamRequestItem::class);
    }

    /** The patient the sample belongs to. */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /** The laboratory handling the sample. */
    public function labo()
    {
        return $this->belongsTo(Labo::class);
    }

    /** The staff member who collected the sample. */
    public function collector()
    {
        return $this->belongsTo(Staff::class, 'collected_by');
    }

    /** Scan history for the sample barcode. */
    public function barcodeLogs()
    {
        return $this->hasMany(SampleBarcodeLog::class);
    }
}
