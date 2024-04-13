<?php

namespace NormanHuth\Lura\Services;

use Illuminate\Contracts\Filesystem\Filesystem as FilesystemInterface;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Str;

class EnvFileService
{
    /**
     * The filesystem instance for the target directory.
     */
    public FilesystemAdapter|FilesystemInterface $targetDisk;

    public function __construct(FilesystemAdapter|FilesystemInterface $targetDisk)
    {
        $this->targetDisk = $targetDisk;
        if (!$this->targetDisk->exists('.env')) {
            $this->targetDisk->put(
                '.env',
                $this->targetDisk->get('.env.example')
            );
        }
    }

    public function addKeys(array|string $keys, string $after = null): void
    {
        $keys = (array) $keys;
        foreach (['.env', '.env.example'] as $file) {
            $inserted = false;
            $lines = Str::splitNewLines(trim($this->targetDisk->get($file)));
            $lines = array_map('trim', $lines);
            $contents = '';

            foreach ($lines as $line) {
                $contents .= $line . "\n";
                if ($after && explode('=', $line)[0] == $after) {
                    $contents .= "\n";
                    foreach ($keys as $key) {
                        $contents .= $key . "=\n";
                    }
                    $contents .= "\n";
                    $inserted = true;
                }
            }

            if (!$inserted) {
                $contents .= "\n";
                foreach ($keys as $key) {
                    $contents .= $key . "=\n";
                }
                $contents .= "\n";
            }

            $contents = preg_replace('/\n{3,}/m', "\n\n", $contents);
            $this->targetDisk->put($file, trim($contents) . "\n");
        }
    }

    public function setValue(string $key, string $value): void
    {
        $this->updateValue($key, $value);
    }

    public function setExampleValue(string $key, string $value): void
    {
        $this->updateValue($key, $value, '.env.example');
    }

    protected function updateValue(string $key, string $value, string $file = '.env'): void
    {
        $lines = Str::splitNewLines(trim($this->targetDisk->get($file)));
        $contents = '';
        foreach ($lines as $line) {
            if (!str_starts_with($line, $key . '=')) {
                $contents .= $line . "\n";

                continue;
            }
            $contents .= $key . '=' . $value . "\n";
        }

        $contents = preg_replace('/\n{3,}/m', "\n\n", $contents);
        $this->targetDisk->put($file, trim($contents) . "\n");
    }
}
