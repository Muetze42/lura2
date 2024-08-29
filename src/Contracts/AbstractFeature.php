<?php

namespace NormanHuth\Lura\Contracts;

use Illuminate\Support\Str;
use NormanHuth\Lura\Commands\InstallLaravelCommand;

abstract class AbstractFeature implements FeatureInterface
{
    /**
     * Determine the key of the feature.
     */
    public static function key(): string
    {
        return Str::kebab(class_basename(get_called_class()));
    }

    /**
     * Determine the name of the feature.
     */
    abstract public static function name(): string;

    /**
     * Determine if this feature should be checked by default if autoloaded.
     */
    public static function default(): bool
    {
        return false;
    }

    /**
     * Determine if this feature should be autoloaded.
     */
    public static function autoload(): bool
    {
        return true;
    }

    /**
     * Determine composer requirements for this feature.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function addComposerRequirement(InstallLaravelCommand $command): array
    {
        return [];
    }

    /**
     * Determine composer dev requirements for this feature.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function addComposerDevRequirement(InstallLaravelCommand $command): array
    {
        return [];
    }

    /**
     * Determine Node package dependencies for this feature.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function addPackageDependency(InstallLaravelCommand $command): array
    {
        return [];
    }

    /**
     * Determine Node package dependencies for this feature.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function addPackageDevDependency(InstallLaravelCommand $command): array
    {
        return [];
    }

    /**
     * Determine composer requirements wich should be removed.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function removeComposerRequirement(InstallLaravelCommand $command): array
    {
        return [];
    }

    /**
     * Determine composer dev requirements wich should be removed.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function removeComposerDevRequirement(InstallLaravelCommand $command): array
    {
        return [];
    }

    /**
     * Determine Node dependency wich should be removed.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function removePackageDependency(InstallLaravelCommand $command): array
    {
        return [];
    }

    /**
     * Determine Node dev dependency wich should be removed.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
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
     * Determine composer scripts for this feature.
     *
     * @return array{string, string|array}
     */
    public static function composerScripts(InstallLaravelCommand $command): array
    {
        return [];
    }

    /**
     * Determine package scripts for this feature.
     *
     * @return array{string, string|array}
     */
    public static function packageScripts(InstallLaravelCommand $command): array
    {
        return [];
    }

    /**
     * Optional load additional features wich not autoloaded.
     */
    public static function load(InstallLaravelCommand $command): array
    {
        return [];
    }
}
