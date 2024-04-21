<?php

namespace NormanHuth\Lura\Features\Laravel;

use NormanHuth\Lura\Commands\InstallLaravelCommand;
use NormanHuth\Lura\Contracts\AbstractFeature;
use NormanHuth\Lura\Support\Package;

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
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function addComposerRequirement(InstallLaravelCommand $command): array
    {
        return [
            new Package('laravel/pennant', '^1.7'),
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
