<?php

namespace NormanHuth\Lura\Support;

class ComposerScript
{
    public string $key;

    public string|array $value;

    public ?string $description;

    public function __construct(string $key, string|array $value, ?string $description = null)
    {
        $this->key = $key;
        $this->value = $value;
        $this->description = $description;
    }
}
