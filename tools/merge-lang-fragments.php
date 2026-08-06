<?php

/**
 * Merges translation fragment files from config/lang-fragments/{fr,en}/
 * into lang/fr.json and lang/en.json.
 *
 * Rules:
 *  - Keys already present in the target file with the SAME value are skipped (no-op).
 *  - Keys already present with a DIFFERENT value are reported as conflicts and NOT overwritten.
 *  - New keys are appended.
 *  - Fragment files are deleted after a successful merge (empty dirs pruned).
 *
 * Usage: php tools/merge-lang-fragments.php [--dry-run]
 */
$dryRun = in_array('--dry-run', $argv, true);

function decodeJson(string $path): array
{
    $raw = file_get_contents($path);
    if ($raw === false || trim($raw) === '') {
        return [];
    }
    $data = json_decode($raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        fwrite(STDERR, "Invalid JSON in {$path}: ".json_last_error_msg().PHP_EOL);
        exit(1);
    }

    return is_array($data) ? $data : [];
}

$conflicts = 0;
$added = 0;
$dupes = 0;

foreach (['fr', 'en'] as $locale) {
    $langPath = __DIR__."/../lang/{$locale}.json";
    $target = decodeJson($langPath);
    $dir = __DIR__."/../config/lang-fragments/{$locale}";

    if (! is_dir($dir)) {
        continue;
    }

    $files = glob($dir.'/*.json');
    $files = $files === false ? [] : $files;

    foreach ($files as $file) {
        $fragment = decodeJson($file);
        foreach ($fragment as $key => $value) {
            if (array_key_exists($key, $target)) {
                if ($target[$key] === $value) {
                    $dupes++;

                    continue;
                }
                fwrite(STDOUT, "[conflict {$locale}] {$key} in ".basename($file)
                    ." ('{$value}') vs existing ('{$target[$key]}')".PHP_EOL);
                $conflicts++;

                continue;
            }
            $target[$key] = $value;
            $added++;
        }
        if (! $dryRun) {
            unlink($file);
        }
    }

    // Prune empty dirs
    if (! $dryRun && is_dir($dir)) {
        $leftovers = glob($dir.'/*');
        if ($leftovers === false || count($leftovers) === 0) {
            rmdir($dir);
        }
    }

    if (! $dryRun) {
        ksort($target);
        $json = json_encode($target, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        file_put_contents($langPath, $json.PHP_EOL);
        fwrite(STDOUT, "[$locale] merged -> ".count($target).' keys total.'.PHP_EOL);
    }
}

fwrite(STDOUT, "Summary: {$added} added, {$dupes} duplicates (same value), {$conflicts} conflicts.".PHP_EOL);
exit($conflicts > 0 ? 1 : 0);
