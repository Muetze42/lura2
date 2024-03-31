<?php

namespace NormanHuth\Luraa\Support;

use Illuminate\Process\Factory;

/**
 * @mixin \Illuminate\Support\Facades\Process
 */
class Process
{
    public static function __callStatic($name, $arguments)
    {
        return (new Factory())->{$name}(...$arguments);
    }
}
