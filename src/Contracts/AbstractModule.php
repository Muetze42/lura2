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
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addComposerRequirement(InstallLaravelCommand $command): array
    {
        return [];
    }

    /**
     * Determine composer dev requirements for this module.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addComposerDevRequirement(InstallLaravelCommand $command): array
    {
        return [];
    }

    /**
     * Determine Node package dependencies for this module.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addPackageDependency(InstallLaravelCommand $command): array
    {
        return [];
    }

    /**
     * Determine Node package dependencies for this module.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addPackageDevDependency(InstallLaravelCommand $command): array
    {
        return [];
    }

    /**
     * Determine composer requirements wich should be removed.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function removeComposerRequirement(InstallLaravelCommand $command): array
    {
        return [];
    }

    /**
     * Determine composer dev requirements wich should be removed.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function removeComposerDevRequirement(InstallLaravelCommand $command): array
    {
        return [];
    }

    /**
     * Determine Node dependency wich should be removed.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function removePackageDependency(InstallLaravelCommand $command): array
    {
        return [];
    }

    /**
     * Determine Node dev dependency wich should be removed.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function removePackageDevDependency(InstallLaravelCommand $command): array
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
