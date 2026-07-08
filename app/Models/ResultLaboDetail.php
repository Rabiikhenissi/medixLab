<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['result_labo_id', 'parameter', 'value', 'status', 'reference_range'])]
class ResultLaboDetail extends Model
{
    protected $table = 'result_labo_details';

    public function resultLabo()
    {
        return $this->belongsTo(ResultLabo::class);
    }
}
