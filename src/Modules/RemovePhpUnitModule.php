<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractModule;
use NormanHuth\Luraa\Support\Package;

class RemovePhpUnitModule extends AbstractModule
{
    /**
     * Determine the name of the module.
     */
    public static function name(): string
    {
        return 'Remove PHPUnit';
    }

    /**
     * Determine composer dev requirements wich should be removed.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function removeComposerDevRequirement(InstallLaravelCommand $command): array
    {
        return [
            new Package('phpunit/phpunit'),
        ];
    }
}
