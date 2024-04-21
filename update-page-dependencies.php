<?php

$versions = json_decode(
    file_get_contents('https://raw.githubusercontent.com/Muetze42/data/main/storage/versions.json'),
    true
);

$versions = array_merge(
    $versions['composer'],
    $versions['npm']
);

$files = glob(__DIR__ . '/src/Features/Laravel/*.php');
foreach ($files as $file) {
    $contents = file_get_contents($file);
    $contents = preg_replace_callback(
        '/new Package\((.*?)\)/s',
        function ($matches) use ($versions) {
            [$package, $version] = sscanf($matches[1], '%s %s');
            if ($version && str_contains($version, '^')) {
                $package = preg_replace('/[^A-Za-z0-9-\/]/', '', $package);

                if (isset($versions[$package])) {
                    $version = $versions[$package];
                    if (! str_starts_with($version, '^')) {
                        $version = '^' . $version;
                    }

                    return "new Package('$package', '$version')";
                }
            }

            return $matches[0];
        },
        $contents
    );
    file_put_contents($file, $contents);
}
