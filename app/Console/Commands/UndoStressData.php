<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Delete every row created by the `stress:seed` command using the id ranges
 * recorded in storage/app/bulk_seed_tracker.json, restoring the database to
 * its pre-seed state.
 *
 * Usage:
 *   php artisan stress:undo
 */
class UndoStressData extends Command
{
    protected $signature = 'stress:undo';

    protected $description = 'Delete all stress data created by stress:seed and restore the database to its prior state';

    /**
     * Run the stress undo command.
     *
     * @return int command exit status (SUCCESS or FAILURE)
     */
    public function handle(): int
    {
        $trackerPath = storage_path('app/bulk_seed_tracker.json');

        // Nothing was seeded: fail gracefully
        if (! file_exists($trackerPath)) {
            $this->error('No bulk seed tracker found. Nothing to undo.');

            return self::FAILURE;
        }

        // Load the recorded batches of id ranges
        $tracker = json_decode(file_get_contents($trackerPath), true);
        if (! is_array($tracker) || empty($tracker['batches'])) {
            $this->error('Tracker is empty or invalid. Nothing to undo.');

            return self::FAILURE;
        }

        // Delete tables in dependency-safe order (children first)
        $tablesInOrder = ['available_exams', 'users', 'patients', 'doctors', 'staff', 'labos', 'exams'];
        $totals = array_fill_keys($tablesInOrder, 0);

        // Delete the tracked rows inside a single transaction
        DB::transaction(function () use ($tracker, &$totals, $tablesInOrder) {
            foreach ($tracker['batches'] as $batch) {
                $ranges = $batch['ranges'] ?? [];
                foreach ($tablesInOrder as $table) {
                    if (empty($ranges[$table])) {
                        continue;
                    }
                    foreach ($ranges[$table] as [$first, $last]) {
                        $totals[$table] += DB::table($table)->whereBetween('id', [$first, $last])->delete();
                    }
                }
            }
        });

        // Remove the tracker now that the data is gone
        unlink($trackerPath);

        // Report how many rows were deleted per table
        foreach ($tablesInOrder as $table) {
            if ($totals[$table] > 0) {
                $this->line("  deleted {$table}: {$totals[$table]}");
            }
        }

        $this->info('Stress data removed. Database restored to its pre-seed state.');

        return self::SUCCESS;
    }
}
