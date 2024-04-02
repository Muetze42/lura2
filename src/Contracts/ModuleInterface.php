<?php

namespace NormanHuth\Luraa\Contracts;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;

interface ModuleInterface
{
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
     * @return array{string: 'package', string: 'version'}
     */
    public static function composerRequirements(InstallLaravelCommand $command): array;

    /**
     * Determine composer dev requirements for this module.
     *
     * @return array{string: 'package', string: 'version'}
     */
    public static function composerDevRequirements(InstallLaravelCommand $command): array;

    /**
     * Determine Node package dependencies for this module.
     *
     * @return array{string: 'package', string: 'version'}
     */
    public static function packageDependency(InstallLaravelCommand $command): array;

    /**
     * Determine Node package dependencies for this module.
     *
     * @return array{string: 'package', string: 'version'}
     */
    public static function packageDevDependency(InstallLaravelCommand $command): array;

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
