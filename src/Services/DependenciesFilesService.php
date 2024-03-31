<?php

namespace NormanHuth\Luraa\Services;

use Illuminate\Support\Arr;
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
            $this->versions = array_merge(
                $response->json('composer'),
                $response->json('npm'),
            );

            $this->versions = Arr::map($this->versions, function (string $version) {
                return str_starts_with($version, '^') ? $version : '^' . $version;
            });
        }
    }

    public function addComposerRepository(array $data): void
    {
        $repositories = data_get($this->dependencies['composer'], 'repositories', []);
        $repositories[] = $data;
        data_set(
            $this->dependencies['composer'],
            'repositories',
            $repositories
        );
    }

    public function addComposerScript(string $key, array|string $value): void
    {
        $value = (array) $value;
        $scripts = data_get($this->dependencies['composer'], 'scripts', []);
        $scripts[$key] = $value;
        data_set(
            $this->dependencies['composer'],
            'scripts',
            $scripts
        );
    }

    public function addComposerRequirement(string $package, string $version, bool $forceVersion = false): void
    {
        $this->dependenciesUpdate($package, $version, $forceVersion);
    }

    public function addComposerDevRequirement(string $package, string $version, bool $forceVersion = false): void
    {
        $this->dependenciesUpdate($package, $version, $forceVersion, 'composer.require-dev');
    }

    public function addPackageDependency(string $package, string $version, bool $forceVersion = false): void
    {
        $this->dependenciesUpdate($package, $version, $forceVersion, 'package.dependencies');
    }

    public function addPackageDevDependency(string $package, string $version, bool $forceVersion = false): void
    {
        $this->dependenciesUpdate($package, $version, $forceVersion, 'package.devDependencies');
    }

    protected function dependenciesUpdate(
        string $package,
        string $version,
        bool $forceVersion = false,
        string $key = 'composer.require'
    ): void {
        if (!$forceVersion) {
            $version = $this->versions[$package] ?? $version;
        }

        $forceVersion ? data_set($this->dependencies, $key . '.' . $package, $version) :
            data_fill($this->dependencies, $key . '.' . $package, $version);
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
