<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CnamNomenclature extends Model
{
    protected $fillable = ['code_cnam', 'exam_name', 'valeur_b', 'taux', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
