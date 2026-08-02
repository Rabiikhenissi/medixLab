<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['labo_id', 'day', 'start_time', 'end_time', 'is_closed', 'date_close', 'is_archive'])]
/**
 * Opening hours for a laboratory, per weekday or a specific closure date.
 */
class WorkingHours extends Model
{
    protected $table = 'working_hours';

    protected function casts(): array
    {
        return [
            'is_closed' => 'boolean',
            'date_close' => 'date',
            'is_archive' => 'boolean',
        ];
    }

    /** The laboratory these hours belong to. */
    public function labo()
    {
        return $this->belongsTo(Labo::class);
    }
}
