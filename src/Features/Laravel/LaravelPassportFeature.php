<?php

namespace NormanHuth\Luraa\Features\Laravel;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractFeature;
use NormanHuth\Luraa\Support\Package;

class LaravelPassportFeature extends AbstractFeature
{
    /**
     * Determine the name of the feature.
     */
    public static function name(): string
    {
        return 'Laravel Passport';
    }

    /**
     * Determine composer requirements for this feature.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addComposerRequirement(InstallLaravelCommand $command): array
    {
        return [
            new Package('laravel/passport', 'v12.0'),
        ];
    }
}
