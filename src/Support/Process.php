<?php

namespace NormanHuth\Luraa\Support;

use Illuminate\Process\Factory;

/**
 * @mixin \Illuminate\Support\Facades\Process
 */
class Process
{
    public static ?int $timeout = null;

    public static function __callStatic($name, $arguments)
    {
        /* @var \Illuminate\Support\Facades\Process $instance */
        $instance = new Factory();

        return $instance->timeout(static::$timeout)->{$name}(...$arguments);
    }
}
