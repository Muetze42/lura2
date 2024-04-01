<?php

$lines = preg_split('/\r\n|\n|\r/', file_get_contents(__DIR__ . '/stubs/README.md'));
$contents = [];

foreach ($lines as $line) {
    if (!str_starts_with($line, 'image:')) {
        $contents[] = $line;
        continue;
    }

    $image = str_replace('image:', '', $line);

    $line = '![' . pathinfo($line, PATHINFO_FILENAME) . ']';
    $line .= '(' . $image . '?v=' . md5_file(__DIR__ . $image) . ')';

    $contents[] = $line;
}

file_put_contents(__DIR__ . '/README.md', implode("\n", $contents));
