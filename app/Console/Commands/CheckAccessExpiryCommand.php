<?php

namespace App\Console\Commands;

use App\Services\ExamRequestService;
use Illuminate\Console\Command;

class CheckAccessExpiryCommand extends Command
{
    protected $signature = 'access:expiry-check';

    protected $description = 'Revoke expired doctor-patient accesses and notify the affected parties';

    public function handle(ExamRequestService $service): int
    {
        try {
            $service->checkAccessExpiry();
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info('Doctor-patient access expiry check completed.');

        return self::SUCCESS;
    }
}
