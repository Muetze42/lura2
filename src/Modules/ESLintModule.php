<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractModule;
use NormanHuth\Luraa\Support\Package;

class ESLintModule extends AbstractModule
{
    /**
     * Determine the name of the module.
     */
    public static function name(): string
    {
        return 'ESLint';
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
     * @return array{string: 'package' => string: 'version'}
     */
    public static function addPackageDevDependency(InstallLaravelCommand $command): array
    {
        return [
            new Package('@babel/plugin-syntax-dynamic-import', '^7.8.3'),
            new Package('@vue/eslint-config-prettier', '^9.0.0'),
            new Package('eslint-plugin-vue', '^9.24.0'),
            new Package('@rushstack/eslint-patch', '^1.10.1'),
        ];
    }

    /**
     * Perform action after create project.
     */
    public static function afterCreateProject(InstallLaravelCommand $command): void
    {
        $command->storage->publish('templates/eslint');
    }
}
