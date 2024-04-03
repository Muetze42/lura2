<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractModule;

use NormanHuth\Luraa\Support\Package;

use function Laravel\Prompts\select;

class LaravelPintModule extends AbstractModule
{
    protected static string $rules = 'psr12-custom';

    /**
     * Determine the name of the module.
     */
    public static function name(): string
    {
        return 'Laravel Pint';
    }

    /**
     * Determine if this module should be checked by default if autoloaded.
     */
    public static function default(): bool
    {
        return true;
    }

    /**
     * Determine composer dev requirements for this module.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addComposerDevRequirement(InstallLaravelCommand $command): array
    {
        return [
            new Package('laravel/pint', '^1.15'),
        ];
    }

    /**
     * Determine composer scripts for this module.
     *
     * @return array{string, string|array}
     */
    public static function composerScripts(InstallLaravelCommand $command): array
    {
        return ['pint' => './vendor/bin/pint'];
    }

    /**
     * Perform action after create project.
     */
    public static function afterCreateProject(InstallLaravelCommand $command): void
    {
        $file = 'templates/pint/' . static::$rules . '.json';
        $command->storage->publish($file, 'pint.json');
    }

    /**
     * Perform action before create project.
     */
    public static function beforeCreateProject(InstallLaravelCommand $command): void
    {
        $rules = array_map(
            fn (string $file) => pathinfo($file, PATHINFO_FILENAME),
            $command->storage->packageDisk->files('templates/pint')
        );

        static::$rules = select(
            label: 'Wich rules should use for Laravel Pint in this project?',
            options: $rules,
            default: static::$rules,
            required: true
        );
    }

    /**
     * Perform action after the composer install process.
     */
    public static function afterComposerInstall(InstallLaravelCommand $command): void
    {
        // Perform action at last in command!
        //$command->runProcess($command->composer . ' pint --ansi');
    }
}
