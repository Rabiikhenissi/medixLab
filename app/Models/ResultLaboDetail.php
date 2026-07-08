<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['result_labo_id', 'parameter', 'value', 'status', 'reference_range', 'is_archive'])]
class ResultLaboDetail extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }
    protected $table = 'result_labo_details';

    public function resultLabo()
    {
        return $this->belongsTo(ResultLabo::class);
    }
}
