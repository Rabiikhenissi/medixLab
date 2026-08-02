<?php

namespace App\Services;

use App\Models\Consumable;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Deduct quantity from a consumable and record stock movement.
     * Fires a low-stock notification if quantity falls below min_quantity.
     *
     * @param  Consumable  $consumable  the consumable to deduct from
     * @param  int  $quantity  quantity to remove
     * @param  string  $reason  reason for the outgoing movement
     */
    public static function deduct(Consumable $consumable, int $quantity, string $reason): void
    {
        DB::transaction(function () use ($consumable, $quantity, $reason) {
            // Reduce the stock quantity and refresh the model
            $consumable->decrement('quantity', $quantity);
            $consumable->refresh();

            // Record the outgoing stock movement
            StockMovement::create([
                'consumable_id' => $consumable->id,
                'type' => 'out',
                'quantity_change' => $quantity,
                'reason' => $reason,
            ]);

            // Fire low-stock alerts
            if ($consumable->quantity <= $consumable->min_quantity) {
                // Notify every staff member of the owning lab
                $lab = $consumable->labo;
                if ($lab) {
                    $staffMembers = $lab->staff;
                    foreach ($staffMembers as $staff) {
                        NotificationService::stockAlert($staff->user_id, $consumable->name, $lab->name);
                    }
                }
            }
        });
    }

    /**
     * Add quantity to a consumable and record stock movement.
     *
     * @param  Consumable  $consumable  the consumable to restock
     * @param  int  $quantity  quantity to add
     * @param  string  $reason  reason for the incoming movement
     */
    public static function add(Consumable $consumable, int $quantity, string $reason): void
    {
        DB::transaction(function () use ($consumable, $quantity, $reason) {
            // Increase the stock quantity
            $consumable->increment('quantity', $quantity);

            // Record the incoming stock movement
            StockMovement::create([
                'consumable_id' => $consumable->id,
                'type' => 'in',
                'quantity_change' => $quantity,
                'reason' => $reason,
            ]);
        });
    }
}
