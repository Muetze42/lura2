<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractModule;

class IdeHelperModule extends AbstractModule
{
    /**
     * Determine the name of the module.
     */
    public static function name(): string
    {
        return 'barryvdh/laravel-ide-helper';
    }

    /**
     * Determine if this module should be installed by default.
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
        return ['barryvdh/laravel-ide-helper' => '^3.0'];
    }
}
