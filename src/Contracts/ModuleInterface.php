<?php

namespace NormanHuth\Luraa\Contracts;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;

interface ModuleInterface
{
    /**
     * Determine the key of the module.
     */
    public static function key(): string;

    /**
     * Determine the name of the module.
     */
    public static function name(): string;

    /**
     * Determine if this module should be installed by default.
     */
    public static function default(): bool;

    /**
     * Determine if this module should be autoloaded.
     */
    public static function autoload(): bool;

    /**
     * Determine composer requirements for this module.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addComposerRequirement(InstallLaravelCommand $command): array;

    /**
     * Determine composer dev requirements for this module.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addComposerDevRequirement(InstallLaravelCommand $command): array;

    /**
     * Determine Node package dependencies for this module.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addPackageDependency(InstallLaravelCommand $command): array;

    /**
     * Determine Node package dependencies for this module.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addPackageDevDependency(InstallLaravelCommand $command): array;

    /**
     * Determine composer requirements wich should be removed.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function removeComposerRequirement(InstallLaravelCommand $command): array;

    /**
     * Determine composer dev requirements wich should be removed.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function removeComposerDevRequirement(InstallLaravelCommand $command): array;

    /**
     * Determine Node dependency wich should be removed.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function removePackageDependency(InstallLaravelCommand $command): array;

    /**
     * Determine Node dev dependency wich should be removed.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function removePackageDevDependency(InstallLaravelCommand $command): array;

    /**
     * Perform action after create project.
     */
    public static function afterCreateProject(InstallLaravelCommand $command): void;

    /**
     * Perform action after the composer install process.
     */
    public static function afterComposerInstall(InstallLaravelCommand $command): void;

    /**
     * Optional load additional modules wich not autoloaded.
     */
    public static function load(InstallLaravelCommand $command): array;
}
