<?php

namespace NormanHuth\Lura\Features\Laravel;

use NormanHuth\Lura\Commands\InstallLaravelCommand;
use NormanHuth\Lura\Contracts\AbstractFeature;
use NormanHuth\Lura\Support\Package;

class LaravelDuskFeature extends AbstractFeature
{
    /**
     * Determine the name of the feature.
     */
    public static function name(): string
    {
        return 'Laravel Dusk';
    }

    /**
     * Determine composer dev requirements for this feature.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function addComposerDevRequirement(InstallLaravelCommand $command): array
    {
        return [
            new Package('laravel/dusk', '^8.1'),
        ];
    }

    /**
     * Perform action after the composer install process.
     */
    public static function afterComposerInstall(InstallLaravelCommand $command): void
    {
        $command->runProcess('php artisan dusk:install --ansi');
    }
}
