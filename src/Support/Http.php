<?php

namespace NormanHuth\Lura\Support;

use Illuminate\Http\Client\Factory;

/**
 * @mixin \Illuminate\Support\Facades\Http
 */
class Http
{
    public static function __callStatic($name, $arguments)
    {
        return (new Factory())->{$name}(...$arguments);
    }
}
