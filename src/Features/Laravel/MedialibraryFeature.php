<?php

namespace NormanHuth\Luraa\Features\Laravel;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractFeature;
use NormanHuth\Luraa\Support\Package;

class MedialibraryFeature extends AbstractFeature
{
    /**
     * Determine the name of the feature.
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
            (int) in_array(PhpLibraryFeature::class, $command->features) . '.stub';
        $command->storage->publish($file, 'config/media-library.php');
        $command->storage->publish('templates/media-library/model.stub', 'app/Models/Media.php');
    }

    /**
     * Determine composer requirements for this feature.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addComposerRequirement(InstallLaravelCommand $command): array
    {
        return [
            new Package('spatie/laravel-medialibrary', '^11.4'),
        ];
    }
}
