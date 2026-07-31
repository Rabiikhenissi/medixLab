<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CnamRate extends Model
{
    protected $fillable = ['code', 'label', 'taux', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function affiliations()
    {
        return $this->hasMany(CnamAffiliation::class);
    }
}
