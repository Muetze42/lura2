<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractModule;

class ActivitylogModule extends AbstractModule
{
    /**
     * Determine the name of the module.
     */
    public static function name(): string
    {
        return 'spatie/laravel-activitylog';
    }

    /**
     * Perform action after create project.
     */
    public static function afterCreateProject(InstallLaravelCommand $command): void
    {
        $command->storage->publish('templates/activity-log/model.stub', 'app/Models/Activity.php');
        $command->storage->publish('templates/activity-log/config.stub', 'config/activitylog.php');
        $command->storage->publish(
            'templates/activity-log/migration.stub',
            'database/migrations/' . $command->getMigrationPrefixedFileName('CreateActivityLogTable')
        );
    }

    /**
     * Determine composer requirements for this module.
     *
     * @return array{string: 'package', string: 'version'}
     */
    public static function composerRequirements(InstallLaravelCommand $command): array
    {
        return ['spatie/laravel-activitylog' => '^4.8'];
    }
}
