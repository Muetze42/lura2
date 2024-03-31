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
        $instance = static::$timeout ? $instance->timeout(static::$timeout) : $instance->forever();

        return $instance->{$name}(...$arguments);
    }
}
