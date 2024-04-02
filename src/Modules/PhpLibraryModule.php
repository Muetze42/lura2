<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractModule;

class PhpLibraryModule extends AbstractModule
{
    /**
     * Determine the name of the module.
     */
    public static function name(): string
    {
        return 'norman-huth/php-library';
    }

    /**
     * Determine composer requirements for this module.
     *
     * @return array{string: 'package', string: 'version'}
     */
    public static function composerRequirements(InstallLaravelCommand $command): array
    {
        return ['norman-huth/php-library' => '^0.0.2'];
    }

    /**
     * Determine if this module should be checked by default if autoloaded.
     */
    public static function default(): bool
    {
        return true;
    }
}
