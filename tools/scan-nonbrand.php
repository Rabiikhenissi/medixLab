<?php

// Lists remaining accented lines per blade file, ignoring brand occurrences ("Medix eSanté" / "eSanté").

$pattern = '/[àâäéèêëîïôöùûüçœÀÂÄÉÈÊËÎÏÔÖÙÛÜÇŒ]/u';
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('resources/views', FilesystemIterator::SKIP_DOTS));
foreach ($it as $file) {
    if ($file->getExtension() !== 'php' || ! str_ends_with($file->getFilename(), 'blade.php')) {
        continue;
    }
    $lines = file($file->getPathname());
    $nonBrand = [];
    foreach ($lines as $i => $line) {
        $clean = str_replace(['Medix eSanté', 'eSanté', 'eSant'], '', $line);
        if (preg_match($pattern, $clean)) {
            $nonBrand[] = ($i + 1).': '.trim($line);
        }
    }
    if (count($nonBrand) > 0) {
        $rel = substr($file->getPathname(), strlen('resources/views/'));
        echo "== $rel\n";
        foreach (array_slice($nonBrand, 0, 20) as $entry) {
            echo "   $entry\n";
        }
        if (count($nonBrand) > 20) {
            echo '   ... +'.(count($nonBrand) - 20)." more\n";
        }
    }
}
