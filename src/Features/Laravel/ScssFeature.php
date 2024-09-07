<?php

namespace NormanHuth\Lura\Features\Laravel;

use NormanHuth\Lura\Commands\InstallLaravelCommand;
use NormanHuth\Lura\Contracts\AbstractFeature;
use NormanHuth\Lura\Support\Package;

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
     * Determine Node package dependencies for this feature.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function addPackageDependency(InstallLaravelCommand $command): array
    {
        return [
            new Package('sass', '^1.78.0'),
            new Package('sass-loader', '^16.0.1'),
        ];
    }

    /**
     * Perform action after create project.
     */
    public static function afterCreateProject(InstallLaravelCommand $command): void
    {
        $command->storage->publish('templates/scss', 'resources/scss');
    }

    /**
     * Perform action after the composer install process.
     */
    public static function afterComposerInstall(InstallLaravelCommand $command): void
    {
        $command->storage->targetDisk->delete('resources/css/app.css');
        $command->storage->targetDisk->deleteDirectory('resources/css');
        if ($command->storage->targetDisk->exists('resources/views/app.blade.php')) {
            $command->storage->targetDisk->put(
                'resources/views/app.blade.php',
                str_replace(
                    'resources/css/app.css',
                    'resources/scss/app.scss',
                    $command->storage->targetDisk->get('resources/views/app.blade.php')
                )
            );
        }
    }
}
