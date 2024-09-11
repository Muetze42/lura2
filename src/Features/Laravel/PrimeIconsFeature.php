<?php

namespace NormanHuth\Lura\Features\Laravel;

use NormanHuth\Lura\AbstractFeature;
use NormanHuth\Lura\Commands\InstallLaravelCommand;
use NormanHuth\Lura\Support\Package;

class PrimeIconsFeature extends AbstractFeature
{
    //
    /**
     * Determine the name of the feature.
     */
    public static function name(): string
    {
        return 'PrimeIcons';
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
        return [
            new Package('primeicons', '7.0.0'),
        ];
    }
}
