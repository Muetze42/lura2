<?php

namespace NormanHuth\Luraa\Features;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Support\Package;

class FontAwesomeProFeature extends FontAwesomeFeature
{
    /**
     * Determine the name of the feature.
     */
    public static function name(): string
    {
        return 'FontAwesomePro';
    }

    /**
     * Determine if this feature should be autoloaded.
     */
    public static function autoload(): bool
    {
        return false;
    }

    /**
     * Determine Node package dependencies for this feature.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addPackageDependency(InstallLaravelCommand $command): array
    {
        return static::packages([
            new Package('pro-duotone-svg-icons', static::$version),
            new Package('pro-light-svg-icons', static::$version),
            new Package('pro-regular-svg-icons', static::$version),
            new Package('pro-solid-svg-icons', static::$version),
        ]);
    }
}
