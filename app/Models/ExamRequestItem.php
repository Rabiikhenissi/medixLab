<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['exam_request_id', 'exam_id', 'is_archive'])]
/**
 * A single exam line within an exam request.
 */
class ExamRequestItem extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }

    protected $table = 'exam_request_items';

    /** The exam request this line belongs to. */
    public function examRequest()
    {
        return $this->belongsTo(ExamRequest::class);
    }

    /** The exam being ordered. */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /** The lab result recorded for this line. */
    public function resultLabo()
    {
        return $this->hasOne(ResultLabo::class);
    }

    /** The biological sample collected for this line. */
    public function sample()
    {
        return $this->hasOne(Sample::class);
    }

    /** Invoice lines billing this exam request item. */
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
