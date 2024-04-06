<?php

namespace NormanHuth\Luraa\Features\Laravel;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractFeature;
use NormanHuth\Luraa\Support\Package;

class LaravelPennantFeature extends AbstractFeature
{
    /**
     * Determine the name of the feature.
     */
    public static function name(): string
    {
        return 'Laravel Pennant';
    }

    /**
     * Determine composer requirements for this feature.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addComposerRequirement(InstallLaravelCommand $command): array
    {
        return [
            new Package('laravel/pennant', '^1.7.0'),
        ];
    }

    /**
     * Perform action after the composer install process.
     */
    public static function afterComposerInstall(InstallLaravelCommand $command): void
    {
        $command->runProcess(
            'php artisan vendor:publish --provider="Laravel\Pennant\PennantServiceProvider" --ansi'
        );
    }
}
