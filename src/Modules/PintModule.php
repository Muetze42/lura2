<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractModule;

use function Laravel\Prompts\select;

class PintModule extends AbstractModule
{
    protected static string $pintRules = 'psr12-custom';

    /**
     * Determine the name of the module.
     */
    public static function name(): string
    {
        return 'Laravel Pints';
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
     * @return array{string: 'package', string: 'version'}
     */
    public static function composerDevRequirements(InstallLaravelCommand $command): array
    {
        return ['laravel/pint' => '^1.15'];
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
        $file = 'templates/pint/' . static::$pintRules . '.json';
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

        static::$pintRules = select(
            label: 'Wich rules should use for Laravel Pint in this project?',
            options: $rules,
            default: static::$pintRules,
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
