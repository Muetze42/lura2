<?php

namespace NormanHuth\Luraa\Features;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractFeature;
use NormanHuth\Luraa\Support\Package;

class IdeHelperFeature extends AbstractFeature
{
    /**
     * Determine the name of the feature.
     */
    public static function name(): string
    {
        return 'barryvdh/laravel-ide-helper';
    }

    /**
     * Determine if this feature should be installed by default.
     */
    public static function default(): bool
    {
        return true;
    }

    /**
     * Determine composer dev requirements for this feature.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addComposerDevRequirement(InstallLaravelCommand $command): array
    {
        return [
            new Package('barryvdh/laravel-ide-helper', '^3.0'),
        ];
    }
}
