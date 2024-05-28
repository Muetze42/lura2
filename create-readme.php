<?php

$lines = preg_split('/\r\n|\n|\r/', file_get_contents(__DIR__.'/stubs/README.md'));
$contents = [];

foreach ($lines as $line) {
    if ($line != '{images}') {
        $contents[] = $line;

        continue;
    }

    $images = glob(__DIR__.'/docs/assets/*.{jpg,png,gif}', GLOB_BRACE);
    sort($images);
    foreach ($images as $image) {
        $line = '!['.pathinfo($image, PATHINFO_FILENAME).']';
        $line .= '(/docs/assets/'.basename($image).'?v='.md5_file($image).')';

        $contents[] = $line;
    }
}

file_put_contents(__DIR__.'/README.md', implode("\n", $contents));
