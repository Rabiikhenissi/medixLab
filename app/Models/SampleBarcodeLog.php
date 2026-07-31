<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SampleBarcodeLog extends Model
{
    protected $fillable = ['sample_id', 'action', 'staff_id', 'location', 'notes'];

    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
