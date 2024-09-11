<?php

namespace NormanHuth\Lura\Features\Laravel;

use NormanHuth\Lura\Commands\InstallLaravelCommand;
use NormanHuth\Lura\AbstractFeature;
use NormanHuth\Lura\Support\Package;

class FontAwesomeFeature extends AbstractFeature
{
    protected static string $version = '^6.6.0';

    protected static function packages(array $merge): array
    {
        return array_merge([
            new Package('@fortawesome/vue-fontawesome', '^3.0.8'),
            new Package('@fortawesome/fontawesome-svg-core', static::$version),
            new Package('@fortawesome/free-brands-svg-icons', static::$version),
        ], $merge);
    }

    /**
     * Determine the name of the feature.
     */
    public static function name(): string
    {
        return 'Font Awesome';
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
            new Package('@fortawesome/free-regular-svg-icons', static::$version),
            new Package('@fortawesome/free-solid-svg-icons', static::$version),
        ]);
    }
}
