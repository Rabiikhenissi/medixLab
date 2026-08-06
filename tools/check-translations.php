<?php

// Verifies every __('key') / @lang('key') / @json(__('key')) used in blade views
// exists in BOTH lang/fr.json and lang/en.json.
// Usage: php tools/check-translations.php

$fr = json_decode(file_get_contents(__DIR__.'/../lang/fr.json'), true);
$en = json_decode(file_get_contents(__DIR__.'/../lang/en.json'), true);
$frKeys = $fr !== null ? $fr : [];
$enKeys = $en !== null ? $en : [];

if ($fr === null || $en === null) {
    fwrite(STDERR, "Invalid lang JSON\n");
    exit(1);
}

// All blade files except those that only contain brand accents are checked.
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__.'/../resources/views', FilesystemIterator::SKIP_DOTS));

$missing = [];
$keyRegex = <<<'REGEX'
/(?:__|@lang|trans)\s*\(\s*(['"])([^'"]+)\1|@json\s*\(\s*__\s*\(\s*(['"])([^'"]+)\3\s*\)/
REGEX;

foreach ($it as $file) {
    if ($file->getExtension() !== 'php' || ! str_ends_with($file->getFilename(), 'blade.php')) {
        continue;
    }
    $content = file_get_contents($file->getPathname());
    if (preg_match_all($keyRegex, $content, $m)) {
        foreach ($m[2] as $i => $key) {
            if (isset($m[4][$i]) && $m[4][$i] !== '') {
                $key = $m[4][$i];
            }
            if ($key === '' || preg_match('/[${}]/', $key) || str_ends_with($key, '.') || str_ends_with($key, '_')) {
                continue;
            }
            $rel = substr($file->getPathname(), strlen('resources/views/'));
            if (! array_key_exists($key, $frKeys)) {
                $missing[] = "$rel : MISSING-FR $key";
            }
            if (! array_key_exists($key, $enKeys)) {
                $missing[] = "$rel : MISSING-EN $key";
            }
        }
    }
}

// Also check JS files for __('..') style keys
$jsIt = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__.'/../resources/js', FilesystemIterator::SKIP_DOTS));
foreach ($jsIt as $file) {
    if ($file->getExtension() !== 'js') {
        continue;
    }
    $content = file_get_contents($file->getPathname());
    if (preg_match_all($keyRegex, $content, $m)) {
        foreach ($m[2] as $i => $key) {
            if (isset($m[4][$i]) && $m[4][$i] !== '') {
                $key = $m[4][$i];
            }
            if ($key === '' || preg_match('/[${}]/', $key) || str_ends_with($key, '.') || str_ends_with($key, '_')) {
                continue;
            }
            $rel = substr($file->getPathname(), strlen('resources/js/'));
            if (! array_key_exists($key, $frKeys)) {
                $missing[] = "$rel : MISSING-FR $key";
            }
            if (! array_key_exists($key, $enKeys)) {
                $missing[] = "$rel : MISSING-EN $key";
            }
        }
    }
}

$missing = array_unique($missing);
sort($missing);

if (count($missing) > 0) {
    echo implode(PHP_EOL, $missing).PHP_EOL;
    echo 'TOTAL MISSING: '.count($missing).PHP_EOL;
    exit(1);
}

echo 'All translation keys resolve in both lang files (fr='.count($frKeys).', en='.count($enKeys).").\n";
