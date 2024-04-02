<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractModule;
use NormanHuth\Luraa\Support\Package;

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
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addComposerRequirement(InstallLaravelCommand $command): array
    {
        return [
            new Package('spatie/laravel-activitylog', '^4.8'),
        ];
    }
}
