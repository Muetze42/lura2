<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractModule;

class LaravelUiModule extends AbstractModule
{
    /**
     * Determine the name of the module.
     */
    public static function name(): string
    {
        return 'Laravel UI';
    }

    /**
     * Determine composer requirements for this module.
     *
     * @return array{string: 'package', string: 'version'}
     */
    public static function composerRequirements(InstallLaravelCommand $command): array
    {
        return ['laravel/ui' => '^4.5'];
    }
}
