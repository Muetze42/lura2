<?php

namespace NormanHuth\Lura\Services;

use Illuminate\Support\Arr;
use NormanHuth\Library\Support\ComposerJson;
use NormanHuth\Lura\Support\ComposerScript;
use NormanHuth\Lura\Support\Http;

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

    public function __construct(?string $packageJsonFile = null, ?string $composerJsonFile = null)
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

        $this->close();
    }

    public function addComposerScript(ComposerScript $script): void
    {
        $scripts = data_get($this->dependencies['composer'], 'scripts', []);
        $scripts[$script->key] = $script->value;
        data_set(
            $this->dependencies['composer'],
            'scripts',
            $scripts
        );

        if ($description = $script->description) {
            $descriptions = data_get($this->dependencies['composer'], 'scripts-descriptions', []);
            $descriptions[$script->key] = $description;
            data_set(
                $this->dependencies['composer'],
                'scripts-descriptions',
                $descriptions
            );
        }

        $this->close();
    }

    public function simplifyComposerScripts(): void
    {
        $scripts = data_get($this->dependencies['composer'], 'scripts', []);

        foreach ($scripts as $key => $value) {
            if (is_array($value) && count($value) == 1) {
                $scripts[$key] = $value[0];
            }
        }

        data_set(
            $this->dependencies['composer'],
            'scripts',
            $scripts
        );
        $this->close();
    }

    public function addScriptAlias(string $script, array|string $target): void
    {
        $aliases = data_get($this->dependencies['composer'], 'scripts-aliases', []);
        $aliases[$script] = (array) $target;
        data_set(
            $this->dependencies['composer'],
            'scripts-aliases',
            $aliases
        );

        $this->close();
    }

    public function addPackageScript(string $key, string $value): void
    {
        $scripts = data_get($this->dependencies['package'], 'scripts', []);
        $scripts[$key] = $value;
        data_set(
            $this->dependencies['package'],
            'scripts',
            $scripts
        );

        $this->close();
    }

    public function addComposerRequirement(string $package, string $version, bool $forceVersion = false): void
    {
        data_forget($this->dependencies, 'composer.require-dev.' . $package);
        data_forget($this->dependencies, 'composer.require.' . $package);
        $this->addDependency($package, $version, $forceVersion);
    }

    public function addComposerDevRequirement(string $package, string $version, bool $forceVersion = false): void
    {
        data_forget($this->dependencies, 'composer.require-dev.' . $package);
        data_forget($this->dependencies, 'composer.require.' . $package);
        $this->addDependency($package, $version, $forceVersion, 'composer.require-dev');
    }

    public function addPackageDependency(string $package, string $version, bool $forceVersion = false): void
    {
        data_forget($this->dependencies, 'package.devDependencies.' . $package);
        data_forget($this->dependencies, 'package.dependencies.' . $package);
        $this->addDependency($package, $version, $forceVersion, 'package.dependencies');
    }

    public function addPackageDevDependency(string $package, string $version, bool $forceVersion = false): void
    {
        data_forget($this->dependencies, 'package.devDependencies.' . $package);
        data_forget($this->dependencies, 'package.dependencies.' . $package);
        $this->addDependency($package, $version, $forceVersion, 'package.devDependencies');
    }

    public function removeComposerRequirement(string $package): void
    {
        $this->removeDependency($package);
    }

    public function removeComposerDevRequirement(string $package): void
    {
        $this->removeDependency($package, 'composer.require-dev');
    }

    public function removePackageDependency(string $package): void
    {
        $this->removeDependency($package, 'package.dependencies');
    }

    public function removePackageDevDependency(string $package): void
    {
        $this->removeDependency($package, 'package.devDependencies');
    }

    protected function removeDependency(string $package, string $key = 'composer.require'): void
    {
        data_set(
            $this->dependencies,
            $key,
            Arr::except(data_get($this->dependencies, $key, []), [$package])
        );

        $this->close();
    }

    protected function addDependency(
        string $package,
        string $version,
        bool $forceVersion = false,
        string $key = 'composer.require'
    ): void {
        if (! $forceVersion) {
            $version = $this->versions[$package] ?? $version;
        }

        $forceVersion ? data_set($this->dependencies, $key . '.' . $package, $version) :
            data_fill($this->dependencies, $key . '.' . $package, $version);

        $this->close();
    }

    protected function close(): void
    {
        ksort($this->dependencies['composer']['require']);
        ksort($this->dependencies['composer']['require-dev']);
        if (!empty($this->dependencies['package']['dependencies'])) {
            ksort($this->dependencies['package']['dependencies']);
        }
        if (!empty($this->dependencies['package']['devDependencies'])) {
            ksort($this->dependencies['package']['devDependencies']);
        }

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
}
