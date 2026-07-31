<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function labo()
    {
        return $this->belongsTo(Labo::class);
    }

    public function examRequest()
    {
        return $this->belongsTo(ExamRequest::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getBalanceAttribute()
    {
        return $this->patient_amount - $this->paid_amount;
    }
}
