<?php

namespace NormanHuth\Lura\Features\Laravel;

use NormanHuth\Lura\Commands\InstallLaravelCommand;
use NormanHuth\Lura\Contracts\AbstractFeature;
use NormanHuth\Lura\Support\Package;

class TailwindCssFeature extends AbstractFeature
{
    /**
     * Determine the name of the feature.
     */
    public static function name(): string
    {
        return 'Tailwind CSS';
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
            new Package('tailwindcss', '^3.4.3'),
            new Package('postcss', '^8.4.38'),
            new Package('autoprefixer', '^10.4.19'),
            new Package('@tailwindcss/forms', '^0.5.7'),
            new Package('tailwind-scrollbar', '^3.1.0'),
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
            if (! $command->storage->targetDisk->exists($file)) {
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
