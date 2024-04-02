<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractModule;
use NormanHuth\Luraa\Support\Package;

class PestPluginModule extends AbstractModule
{
    /**
     * Determine the name of the module.
     */
    public static function name(): string
    {
        return 'pestphp/pest-plugin-laravel';
    }

    /**
     * Determine composer dev requirements wich should be removed.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addComposerDevRequirement(InstallLaravelCommand $command): array
    {
        return [
            new Package('pestphp/pest-plugin-laravel', '^2.3'),
        ];
    }
}
