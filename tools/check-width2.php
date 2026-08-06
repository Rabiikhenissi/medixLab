<?php
foreach (['resources/views/profile/show.blade.php', 'resources/views/auth/two-factor-login.blade.php', 'resources/views/profile/two-factor.blade.php'] as $f) {
    $c = file_get_contents($f);
    if (preg_match('/max-w-\w+/', $c, $m)) {
        echo basename($f) . ": " . $m[0] . PHP_EOL;
    } else {
        echo basename($f) . ": no max-w class" . PHP_EOL;
    }
}
