<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractModule;

class PermissionModule extends AbstractModule
{
    /**
     * Determine the name of the module.
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
     * Determine composer requirements for this module.
     *
     * @return array{string: 'package', string: 'version'}
     */
    public static function composerRequirements(InstallLaravelCommand $command): array
    {
        return ['spatie/laravel-permission' => '^6.4'];
    }
}
