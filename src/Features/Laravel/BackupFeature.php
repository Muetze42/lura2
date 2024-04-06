<?php

namespace NormanHuth\Luraa\Features\Laravel;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractFeature;
use NormanHuth\Luraa\Support\Package;

class BackupFeature extends AbstractFeature
{
    /**
     * Determine the name of the feature.
     */
    public static function name(): string
    {
        return 'spatie/laravel-backup';
    }

    /**
     * Determine composer requirements for this feature.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addComposerRequirement(InstallLaravelCommand $command): array
    {
        return [
            new Package('spatie/laravel-backup', '^8.6'),
        ];
    }

    /**
     * Perform action after the composer install process.
     */
    public static function afterComposerInstall(InstallLaravelCommand $command): void
    {
        $command->runProcess(
            'php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider" --ansi'
        );
    }
}
