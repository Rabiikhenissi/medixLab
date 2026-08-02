<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A CNAM reimbursement rate that patient affiliations can subscribe to.
 */
class CnamRate extends Model
{
    protected $fillable = ['code', 'label', 'taux', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    /** Patient affiliations using this rate. */
    public function affiliations()
    {
        return $this->hasMany(CnamAffiliation::class);
    }
}
