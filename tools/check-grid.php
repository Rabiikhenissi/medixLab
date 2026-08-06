<?php
$c = file_get_contents('resources/css/app.css');
if (preg_match('/profile-grid[^}]*}/s', $c, $m)) {
    echo substr($m[0], 0, 500) . PHP_EOL;
} else {
    echo "profile-grid not found in CSS" . PHP_EOL;
}
