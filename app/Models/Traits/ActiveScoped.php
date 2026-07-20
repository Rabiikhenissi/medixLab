<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ActiveScoped
{
    /**
     * Scope a query to only include active (non-archived) records.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where($this->getTable() . '.is_archive', false);
    }
}
