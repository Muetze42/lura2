<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractModule;

class FontAwesomeModule extends AbstractModule
{
    protected static string $version = '^6.5.1';
    protected static function packages(array $merge): array
    {
        return array_merge([
            '@fortawesome/vue-fontawesome' => '^3.0.6',
            '@fortawesome/fontawesome-svg-core' => static::$version,
            '@fortawesome/free-brands-svg-icons' => static::$version,
        ], $merge);
    }

    /**
     * Determine the name of the module.
     */
    public static function name(): string
    {
        return 'FontAwesome';
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
            'free-regular-svg-icons' => static::$version,
            'free-solid-svg-icons' => static::$version,
        ]);
    }
}
