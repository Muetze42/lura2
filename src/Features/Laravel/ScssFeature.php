<?php

namespace NormanHuth\Luraa\Features\Laravel;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractFeature;

class ScssFeature extends AbstractFeature
{
    /**
     * Determine the name of the feature.
     */
    public static function name(): string
    {
        return 'CSS instead of CSS';
    }

    /**
     * Determine if this feature should be checked by default if autoloaded.
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