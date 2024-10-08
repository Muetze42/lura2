<?php

namespace NormanHuth\Lura\Features\Laravel;

use NormanHuth\Lura\Commands\InstallLaravelCommand;
use NormanHuth\Lura\AbstractFeature;
use NormanHuth\Lura\Support\Package;

class LaravelSanctumFeature extends AbstractFeature
{
    /**
     * Determine the name of the feature.
     */
    public static function name(): string
    {
        return 'Laravel Sanctum';
    }

    /**
     * Perform action after create project.
     */
    public static function afterCreateProject(InstallLaravelCommand $command): void
    {
        $command->env->addKeys('SANCTUM_TOKEN_PREFIX', 'APP_URL');
    }

    /**
     * Determine composer requirements for this feature.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function addComposerRequirement(InstallLaravelCommand $command): array
    {
        return [
            new Package('laravel/sanctum', '^4.0'),
        ];
    }

    /**
     * Perform action after the composer install process.
     */
    public static function afterComposerInstall(InstallLaravelCommand $command): void
    {
        //$command->runProcess('php artisan vendor:publish --tag=sanctum-migrations --ansi');
        $command->storage->publish(
            'templates/sanctum/migration.stub',
            'database/migrations/' . $command->getMigrationPrefixedFileName('create_personal_access_tokens_table')
        );
        $command->line('Publishing [sanctum-migrations] assets.', 'info');
        $command->runProcess('php artisan vendor:publish --tag=sanctum-config --ansi');
    }
}
