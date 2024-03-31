<?php

namespace NormanHuth\Luraa\Services;

class EnvFile
{
    protected string $file;
    protected string $contents;

    public function __construct($file)
    {
        $this->file = $file;
        $this->contents = file_get_contents($file);
    }

    public function close(): void
    {
        file_put_contents($this->file, trim($this->contents) . "\n");
    }
}
