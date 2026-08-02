<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Bill issued for an exam request, split between CNAM and patient amounts.
 */
class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 'patient_id', 'labo_id', 'exam_request_id',
        'status', 'total_amount', 'cnam_amount', 'patient_amount',
        'paid_amount', 'payment_method', 'payment_reference', 'paid_at', 'notes',
    ];

    protected function casts(): array
    {
        return ['paid_at' => 'datetime'];
    }

    /** The patient the invoice is billed to. */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /** The laboratory that issued the invoice. */
    public function labo()
    {
        return $this->belongsTo(Labo::class);
    }

    /** The exam request the invoice covers. */
    public function examRequest()
    {
        return $this->belongsTo(ExamRequest::class);
    }

    /** Individual billed lines on this invoice. */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /** Payments registered against this invoice. */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /** Remaining amount still owed by the patient. */
    public function getBalanceAttribute()
    {
        return $this->patient_amount - $this->paid_amount;
    }
}
