<?php

namespace NormanHuth\Luraa\Services;

use NormanHuth\Library\Support\ComposerJson;
use NormanHuth\Luraa\Support\Http;

class DependenciesFilesService
{
    protected ?string $packageJsonFile;

    protected ?string $composerJsonFile;

    protected array $dependencies = [
        'composer' => null,
        'package' => null,
    ];

    protected array $versions = [];

    protected string $versionsSource = 'https://raw.githubusercontent.com/Muetze42/data/main/storage/versions.json';

    public function __construct(string $packageJsonFile = null, string $composerJsonFile = null)
    {
        $this->packageJsonFile = $packageJsonFile;
        $this->composerJsonFile = $composerJsonFile;

        if ($packageJsonFile) {
            $this->dependencies['package'] = json_decode(file_get_contents($packageJsonFile), true);
        }
        if ($composerJsonFile) {
            $this->dependencies['composer'] = json_decode(file_get_contents($composerJsonFile), true);
        }

        $response = Http::get($this->versionsSource);
        if ($response->successful()) {
            $this->versions = $response->json();
        }
    }

    protected function dependenciesUpdate()
    {
    }

    public function close(): void
    {
        if ($this->dependencies['package']) {
            file_put_contents(
                $this->packageJsonFile,
                json_encode(
                    $this->dependencies['package'],
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                )
            );
        }
        if ($this->composerJsonFile) {
            $this->dependencies['composer'] = ComposerJson::sort($this->dependencies['composer']);
            file_put_contents(
                $this->composerJsonFile,
                json_encode(
                    $this->dependencies['composer'],
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                )
            );
        }
    }

    protected function sortJson(): void
    {
        // Todo
    }
}
