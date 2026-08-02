<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

/**
 * Lists available heroicon names for icon pickers in the admin UI.
 */
class Heroicons
{
    /** Return the names of all outline heroicons shipped by the package. */
    public static function all()
    {
        $path = base_path(
            'vendor/blade-ui-kit/blade-heroicons/resources/svg'
        );

        if (! File::exists($path)) {
            return [];
        }

        return collect(File::files($path))
            ->map(function ($file) {

                $name = $file->getFilenameWithoutExtension();

                // Keep only outline icons
                if (! str_starts_with($name, 'o-')) {
                    return null;
                }

                // Remove o- prefix
                return substr($name, 2);
            })
            ->filter()
            ->sort()
            ->values()
            ->toArray();
    }
}
