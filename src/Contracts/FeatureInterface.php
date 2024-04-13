<?php

namespace NormanHuth\Lura\Contracts;

use NormanHuth\Lura\Commands\InstallLaravelCommand;

interface FeatureInterface
{
    /**
     * Determine the key of the feature.
     */
    public static function key(): string;

    /**
     * Determine the name of the feature.
     */
    public static function name(): string;

    /**
     * Determine if this feature should be installed by default.
     */
    public static function default(): bool;

    /**
     * Determine if this feature should be autoloaded.
     */
    public static function autoload(): bool;

    /**
     * Determine composer requirements for this feature.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function addComposerRequirement(InstallLaravelCommand $command): array;

    /**
     * Determine composer dev requirements for this feature.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function addComposerDevRequirement(InstallLaravelCommand $command): array;

    /**
     * Determine Node package dependencies for this feature.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function addPackageDependency(InstallLaravelCommand $command): array;

    /**
     * Determine Node package dependencies for this feature.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function addPackageDevDependency(InstallLaravelCommand $command): array;

    /**
     * Determine composer requirements wich should be removed.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function removeComposerRequirement(InstallLaravelCommand $command): array;

    /**
     * Determine composer dev requirements wich should be removed.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function removeComposerDevRequirement(InstallLaravelCommand $command): array;

    /**
     * Determine Node dependency wich should be removed.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function removePackageDependency(InstallLaravelCommand $command): array;

    /**
     * Determine Node dev dependency wich should be removed.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
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
     * Optional load additional features wich not autoloaded.
     */
    public static function load(InstallLaravelCommand $command): array;
}
