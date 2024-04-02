<?php

namespace NormanHuth\Luraa\Contracts;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;

abstract class AbstractModule implements ModuleInterface
{
    /**
     * Determine the name of the module.
     */
    abstract public static function name(): string;

    /**
     * Determine if this module should be checked by default if autoloaded.
     */
    public static function default(): bool
    {
        return false;
    }

    /**
     * Determine if this module should be autoloaded.
     */
    public static function autoload(): bool
    {
        return true;
    }

    /**
     * Determine composer requirements for this module.
     *
     * @return array{string: 'package', string: 'version'}
     */
    public static function composerRequirements(InstallLaravelCommand $command): array
    {
        return [];
    }

    /**
     * Determine composer dev requirements for this module.
     *
     * @return array{string: 'package', string: 'version'}
     */
    public static function composerDevRequirements(InstallLaravelCommand $command): array
    {
        return [];
    }

    /**
     * Determine Node package dependencies for this module.
     *
     * @return array{string: 'package', string: 'version'}
     */
    public static function packageDependency(InstallLaravelCommand $command): array
    {
        return [];
    }

    /**
     * Determine Node package dependencies for this module.
     *
     * @return array{string: 'package', string: 'version'}
     */
    public static function packageDevDependency(InstallLaravelCommand $command): array
    {
        return [];
    }

    /**
     * Perform action before create project.
     */
    public static function beforeCreateProject(InstallLaravelCommand $command): void
    {
        //
    }

    /**
     * Perform action after create project.
     */
    public static function afterCreateProject(InstallLaravelCommand $command): void
    {
        //
    }

    /**
     * Perform action after the composer install process.
     */
    public static function afterComposerInstall(InstallLaravelCommand $command): void
    {
        //
    }

    /**
     * Determine composer scripts for this module.
     *
     * @return array{string, string|array}
     */
    public static function composerScripts(InstallLaravelCommand $command): array
    {
        return [];
    }

    /**
     * Optional load additional modules wich not autoloaded.
     */
    public static function load(InstallLaravelCommand $command): array
    {
        return [];
    }
}
