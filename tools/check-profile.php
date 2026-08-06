<?php
$c = file_get_contents('resources/views/profile/show.blade.php');
echo substr($c, 0, 800) . PHP_EOL;
echo "..." . PHP_EOL;
// Find the main container
if (preg_match('/<div class="[^"]*max-w[^"]*"[^>]*>/', $c, $m)) {
    echo "Container: " . $m[0] . PHP_EOL;
}
// Find all div classes at the start of content section
if (preg_match('/@section\(\'content\'\)(.*?)@endsection/s', $c, $m)) {
    $content = $m[1];
    // Find first few div classes
    preg_match_all('/<div class="([^"]+)"/', $content, $matches);
    foreach (array_slice($matches[1], 0, 5) as $cls) {
        echo "Div class: " . $cls . PHP_EOL;
    }
}
