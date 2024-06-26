<?php

namespace NormanHuth\Lura\Features\Laravel;

use NormanHuth\Lura\Commands\InstallLaravelCommand;
use NormanHuth\Lura\Support\Package;

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
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function addPackageDependency(InstallLaravelCommand $command): array
    {
        return static::packages([
            new Package('@fortawesome/pro-duotone-svg-icons', static::$version),
            new Package('@fortawesome/pro-light-svg-icons', static::$version),
            new Package('@fortawesome/pro-regular-svg-icons', static::$version),
            new Package('@fortawesome/pro-solid-svg-icons', static::$version),
        ]);
    }
}
