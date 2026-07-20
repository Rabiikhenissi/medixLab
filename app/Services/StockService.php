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
     */
    public static function deduct(Consumable $consumable, int $quantity, string $reason): void
    {
        DB::transaction(function () use ($consumable, $quantity, $reason) {
            $consumable->decrement('quantity', $quantity);
            $consumable->refresh();

            StockMovement::create([
                'consumable_id'   => $consumable->id,
                'type'            => 'out',
                'quantity_change' => $quantity,
                'reason'          => $reason,
            ]);

            // Fire low-stock alerts
            if ($consumable->quantity <= $consumable->min_quantity) {
                $lab      = $consumable->labo;
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
     */
    public static function add(Consumable $consumable, int $quantity, string $reason): void
    {
        DB::transaction(function () use ($consumable, $quantity, $reason) {
            $consumable->increment('quantity', $quantity);

            StockMovement::create([
                'consumable_id'   => $consumable->id,
                'type'            => 'in',
                'quantity_change' => $quantity,
                'reason'          => $reason,
            ]);
        });
    }
}
