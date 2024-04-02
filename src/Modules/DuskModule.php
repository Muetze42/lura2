<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractModule;

class DuskModule extends AbstractModule
{
    /**
     * Determine the name of the module.
     */
    public static function name(): string
    {
        return 'Laravel Dusk';
    }

    /**
     * Determine composer dev requirements for this module.
     *
     * @return array{string: 'package', string: 'version'}
     */
    public static function composerDevRequirements(InstallLaravelCommand $command): array
    {
        return ['laravel/dusk' => '^8.1'];
    }
}
