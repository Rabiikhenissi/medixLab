<?php
$c = file_get_contents('resources/views/profile/show.blade.php');
if (preg_match('/max-w/', $c, $m)) {
    echo "Found: " . $m[0] . PHP_EOL;
} else {
    echo "No max-w found in profile/show.blade.php" . PHP_EOL;
}

// Also check what the 2FA login page uses
$c2 = file_get_contents('resources/views/auth/two-factor-login.blade.php');
if (preg_match('/max-w/', $c2, $m2)) {
    echo "2FA login uses: " . $m2[0] . PHP_EOL;
}

// Check profile/two-factor
$c3 = file_get_contents('resources/views/profile/two-factor.blade.php');
if (preg_match('/max-w/', $c3, $m3)) {
    echo "profile/two-factor uses: " . $m3[0] . PHP_EOL;
}
