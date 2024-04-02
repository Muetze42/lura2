<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractModule;
use NormanHuth\Luraa\Support\Package;

class FontAwesomeModule extends AbstractModule
{
    protected static string $version = '^6.5.1';

    protected static function packages(array $merge): array
    {
        return array_merge([
            new Package('@fortawesome/vue-fontawesome', '^3.0.6'),
            new Package('@fortawesome/fontawesome-svg-core', static::$version),
            new Package('@fortawesome/free-brands-svg-icons', static::$version),
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
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addPackageDependency(InstallLaravelCommand $command): array
    {
        return static::packages([
            new Package('free-regular-svg-icons', static::$version),
            new Package('free-solid-svg-icons', static::$version),
        ]);
    }
}
