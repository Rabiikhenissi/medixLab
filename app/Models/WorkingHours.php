<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['labo_id', 'day', 'start_time', 'end_time', 'is_closed', 'date_close'])]
class WorkingHours extends Model
{
    protected $table = 'working_hours';

    protected function casts(): array
    {
        return [
            'is_closed' => 'boolean',
            'date_close' => 'date',
        ];
    }

    public function labo()
    {
        return $this->belongsTo(Labo::class);
    }
}
