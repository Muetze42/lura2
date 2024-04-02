<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;

class FontAwesomeProModule extends FontAwesomeModule
{
    /**
     * Determine the name of the module.
     */
    public static function name(): string
    {
        return 'FontAwesomePro';
    }

    /**
     * Determine if this module should be autoloaded.
     */
    public static function autoload(): bool
    {
        return false;
    }

    /**
     * Determine Node package dependencies for this module.
     *
     * @return array{string: 'package', string: 'version'}
     */
    public static function packageDependency(InstallLaravelCommand $command): array
    {
        return static::packages([
            'pro-duotone-svg-icons' => static::$version,
            'pro-light-svg-icons' => static::$version,
            'pro-regular-svg-icons' => static::$version,
            'pro-solid-svg-icons' => static::$version,
        ]);
    }
}
