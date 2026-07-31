<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id', 'exam_id', 'exam_request_item_id', 'description',
        'quantity', 'unit_price', 'total', 'cnam_code', 'valeur_b', 'cnam_coverage',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function examRequestItem()
    {
        return $this->belongsTo(ExamRequestItem::class);
    }
}
