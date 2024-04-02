<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractModule;
use NormanHuth\Luraa\Support\Package;

class SentryModule extends AbstractModule
{
    /**
     * Determine the name of the module.
     */
    public static function name(): string
    {
        return 'Sentry';
    }

    /**
     * Determine if this module should be checked by default if autoloaded.
     */
    public static function default(): bool
    {
        return true;
    }

    /**
     * Perform action after create project.
     */
    public static function afterCreateProject(InstallLaravelCommand $command): void
    {
        $command->env->addKeys([
            'SENTRY_LARAVEL_DSN',
            'SENTRY_TRACES_SAMPLE_RATE',
            'VITE_SENTRY_DSN_PUBLIC',
            'SENTRY_AUTH_TOKEN',
            'VITE_SENTRY_AUTH_TOKEN',
        ], 'APP_URL');
        $command->env->setValue('VITE_SENTRY_DSN_PUBLIC', '"${SENTRY_LARAVEL_DSN}"');
        $command->env->setExampleValue('VITE_SENTRY_DSN_PUBLIC', '"${SENTRY_LARAVEL_DSN}"');

        $command->storage->publish('templates/config/sentry.php', 'config/sentry.php');
    }

    /**
     * Determine composer requirements for this module.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addComposerRequirement(InstallLaravelCommand $command): array
    {
        return [
            new Package('sentry/sentry-laravel', '^4.4'),
        ];
    }

    /**
     * Determine Node package dependencies for this module.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addPackageDependency(InstallLaravelCommand $command): array
    {
        if (in_array(InertiaJsModule::class, $command->modules)) {
            return [
                new Package('@sentry/vite-plugin', '^2.16.0'),
                new Package('@sentry/vue', '^7.109.0'),
            ];
        }

        return [];
    }
}
