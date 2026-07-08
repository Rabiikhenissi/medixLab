<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['consumable_id', 'quantity_change', 'type', 'reason', 'is_archive'])]
class StockMovement extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }
    protected $table = 'stock_movements';

    public function consumable()
    {
        return $this->belongsTo(Consumable::class);
    }
}
