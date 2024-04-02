<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractModule;

class HeadlessUIVueModule extends AbstractModule
{
    /**
     * Determine the name of the module.
     */
    public static function name(): string
    {
        return 'Headless UI Vue';
    }

    /**
     * Determine if this module should be autoloaded.
     */
    public static function autoload(): bool
    {
        return false;
    }

    /**
     * Determine Node package dependencies for this module.
     *
     * @return array{string: 'package', string: 'version'}
     */
    public static function packageDependency(InstallLaravelCommand $command): array
    {
        return ['@headlessui/vue' => '^1.7.19'];
    }
}
