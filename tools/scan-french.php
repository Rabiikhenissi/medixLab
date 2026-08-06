<?php

// Scans resources/views for accented French characters (UTF-8 aware).

$pattern = '/[àâäéèêëîïôöùûüçœÀÂÄÉÈÊËÎÏÔÖÙÛÜÇŒ]/u';
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views', FilesystemIterator::SKIP_DOTS));
$rows = [];
foreach ($it as $file) {
    if ($file->getExtension() !== 'php' || ! str_ends_with($file->getFilename(), 'blade.php')) {
        continue;
    }
    $content = file_get_contents($file->getPathname());
    $count = preg_match_all($pattern, $content);
    if ($count > 0) {
        $rel = substr($file->getPathname(), strlen('resources/views/'));
        $rows[] = sprintf('%-55s %d', $rel, $count);
    }
}
sort($rows);
echo implode(PHP_EOL, $rows).PHP_EOL;
echo 'TOTAL FILES: '.count($rows).PHP_EOL;
