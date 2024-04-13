<?php

namespace NormanHuth\Lura\Features\Laravel;

use NormanHuth\Lura\Commands\InstallLaravelCommand;
use NormanHuth\Lura\Contracts\AbstractFeature;
use NormanHuth\Lura\Support\Package;

class PermissionFeature extends AbstractFeature
{
    /**
     * Determine the name of the feature.
     */
    public static function name(): string
    {
        return 'spatie/laravel-permission';
    }

    /**
     * Perform action after create project.
     */
    public static function afterCreateProject(InstallLaravelCommand $command): void
    {
        $command->storage->publish('templates/laravel-permission/config.stub', 'config/permission.php');
        $command->storage->publish(
            'templates/laravel-permission/migration.stub',
            'database/migrations/' . $command->getMigrationPrefixedFileName('CreatePermissionsTables')
        );
        $command->storage->publish('templates/laravel-permission/Permission.stub', 'app/Models/Permission.php');
        $command->storage->publish('templates/laravel-permission/Role.stub', 'app/Models/Role.php');
    }

    /**
     * Determine composer requirements for this feature.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function addComposerRequirement(InstallLaravelCommand $command): array
    {
        return [
            new Package('spatie/laravel-permission', '^6.4'),
        ];
    }
}
