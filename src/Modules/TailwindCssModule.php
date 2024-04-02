<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractModule;
use NormanHuth\Luraa\Support\Package;

class TailwindCssModule extends AbstractModule
{
    /**
     * Determine the name of the module.
     */
    public static function name(): string
    {
        return 'Tailwind CSS';
    }

    /**
     * Determine if this module should be checked by default if autoloaded.
     */
    public static function default(): bool
    {
        return true;
    }

    /**
     * Determine Node package dependencies for this module.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addPackageDependency(InstallLaravelCommand $command): array
    {
        return [
            new Package('tailwindcss', '^3.4.3'),
            new Package('postcss', '^8.4.38'),
            new Package('autoprefixer', '^10.4.19'),
            new Package('autoprefixer', '^10.4.19'),
            new Package('@tailwindcss/forms', '^0.5.7'),
            new Package('tailwind-scrollbar', '^3.0.5'),
        ];
    }

    /**
     * Perform action after create project.
     */
    public static function afterCreateProject(InstallLaravelCommand $command): void
    {
        $command->storage->publish('templates/tailwind.config.js');
        $command->storage->publish('templates/postcss.config.js');
    }

    /**
     * Perform action after the composer install process.
     */
    public static function afterComposerInstall(InstallLaravelCommand $command): void
    {
        foreach (['resources/scss/app.scss', 'resources/css/app.css'] as $file) {
            if (!$command->storage->targetDisk->exists($file)) {
                continue;
            }
            $command->storage->targetDisk->put(
                $file,
                "@tailwind base;\n@tailwind components;\n@tailwind utilities;\n\n" .
                    $command->storage->targetDisk->get($file)
            );
        }
    }
}
