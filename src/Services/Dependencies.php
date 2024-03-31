<?php

namespace NormanHuth\Luraa\Services;

class Dependencies
{
    protected ?string $packageJsonFile;

    protected ?string $composerJsonFile;

    protected ?array $packageJson = null;

    protected ?array $composerJson = null;

    public function __construct(string $packageJsonFile = null, string $composerJsonFile = null)
    {
        $this->packageJsonFile = $packageJsonFile;
        $this->composerJsonFile = $composerJsonFile;

        if ($packageJsonFile) {
            $this->packageJson = json_decode(file_get_contents($packageJsonFile), true);
        }
        if ($composerJsonFile) {
            $this->composerJson = json_decode(file_get_contents($composerJsonFile), true);
        }
    }

    public function close(): void
    {
        if ($this->packageJsonFile) {
            file_put_contents(
                $this->packageJsonFile,
                json_encode($this->packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
        }
        if ($this->composerJsonFile) {
            file_put_contents(
                $this->composerJsonFile,
                json_encode($this->composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
        }
    }

    protected function sortJson(): void
    {
        // Todo
    }
}
