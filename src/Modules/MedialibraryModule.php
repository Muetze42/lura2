<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractModule;

class MedialibraryModule extends AbstractModule
{
    /**
     * Determine the name of the module.
     */
    public static function name(): string
    {
        return 'spatie/laravel-medialibrary';
    }

    /**
     * Perform action after create project.
     */
    public static function afterCreateProject(InstallLaravelCommand $command): void
    {
        $file = 'templates/media-library/config.' .
            (int) in_array(PhpLibraryModule::class, $command->modules) . '.stub';
        $command->storage->publish($file, 'config/media-library.php');
        $command->storage->publish('templates/media-library/model.stub', 'app/Models/Media.php');
    }

    /**
     * Determine composer requirements for this module.
     *
     * @return array{string: 'package', string: 'version'}
     */
    public static function composerRequirements(InstallLaravelCommand $command): array
    {
        return ['spatie/laravel-medialibrary' => '^11.4'];
    }
}
