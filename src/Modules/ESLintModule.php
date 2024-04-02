<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractModule;

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
    public static function packageDevDependency(InstallLaravelCommand $command): array
    {
        return [
            '@babel/plugin-syntax-dynamic-import' => '^7.8.3',
            '@vue/eslint-config-prettier' => '^9.0.0',
            'eslint-plugin-vue' => '^9.24.0',
            '@rushstack/eslint-patch' => '^1.10.1',
        ];
    }

    /**
     * Perform action after create project.
     */
    public static function afterCreateProject(InstallLaravelCommand $command): void
    {
        $command->storage->publish('templates/eslint');
    }

    /**
     * Perform action after the composer install process.
     */
    public static function afterComposerInstall(InstallLaravelCommand $command): void
    {
        $command->runProcess('php artisan dusk:install --ansi');
    }
}
