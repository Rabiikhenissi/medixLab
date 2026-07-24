<?php

use App\Services\TranslationService;

if (! function_exists('db_trans')) {
    /**
     * Translate a key via the database-backed TranslationService.
     *
     * Usage in Blade:
     *   {{ db_trans('dashboard.title') }}
     *
     * Falls back to the raw key when no record is found.
     */
    function db_trans(string $key): string
    {
        return app(TranslationService::class)->translate($key);
    }
}
