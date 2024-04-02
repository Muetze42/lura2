<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractModule;

class ScssModule extends AbstractModule
{
    /**
     * Determine the name of the module.
     */
    public static function name(): string
    {
        return 'CSS instead of CSS';
    }

    /**
     * Determine if this module should be checked by default if autoloaded.
     */
    public static function default(): bool
    {
        return true;
    }

    /**
     * Perform action after create project.
     */
    public static function afterCreateProject(InstallLaravelCommand $command): void
    {
        $command->storage->publish('templates/scss', 'resources/scss');
        $command->storage->targetDisk->deleteDirectory('resources/css');
    }
}
