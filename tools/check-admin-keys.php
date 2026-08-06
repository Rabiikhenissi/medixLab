<?php

$fr = json_decode(file_get_contents('config/lang-fragments/fr/admin.json'), true);
$en = json_decode(file_get_contents('config/lang-fragments/en/admin.json'), true);
if (! $fr || ! $en) {
    echo "JSON ERROR\n";
    exit(1);
}
$d1 = array_diff(array_keys($fr), array_keys($en));
$d2 = array_diff(array_keys($en), array_keys($fr));
echo 'FR keys: '.count($fr)."\n";
echo 'EN keys: '.count($en)."\n";
echo 'In FR not EN: '.implode(', ', $d1)."\n";
echo 'In EN not FR: '.implode(', ', $d2)."\n";
