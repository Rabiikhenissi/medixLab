<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['consumable_id', 'quantity_change', 'type', 'reason', 'is_archive'])]
/**
 * Audit trail of a quantity in/out change on a consumable's stock.
 */
class StockMovement extends Model
{
    protected function casts(): array
    {
        return [
            'is_archive' => 'boolean',
        ];
    }

    protected $table = 'stock_movements';

    /** The consumable whose stock changed. */
    public function consumable()
    {
        return $this->belongsTo(Consumable::class);
    }
}
