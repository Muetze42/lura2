<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractModule;

use NormanHuth\Luraa\Support\Package;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

class InertiaJsModule extends AbstractModule
{
    /**
     * Determine the name of the module.
     */
    public static function name(): string
    {
        return 'Inertia.js';
    }

    /**
     * Determine if this module should be checked by default if autoloaded.
     */
    public static function default(): bool
    {
        return true;
    }

    /**
     * Determine composer requirements for this module.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addComposerRequirement(InstallLaravelCommand $command): array
    {
        return [
            new Package('inertiajs/inertia-laravel', '^1.0'),
        ];
    }

    /**
     * Determine Node package dependencies for this module.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addPackageDependency(InstallLaravelCommand $command): array
    {
        return [
            new Package('@inertiajs/vue3', '^1.0.15'),
        ];
    }

    /**
     * Perform action after create project.
     */
    public static function afterCreateProject(InstallLaravelCommand $command): void
    {
        $file = 'templates/vite.config.' . (int) in_array(SentryModule::class, $command->modules) . '.js';
        $command->storage->publish($file, 'vite.config.js');
    }

    /**
     * Perform action after the composer install process.
     */
    public static function afterComposerInstall(InstallLaravelCommand $command): void
    {
        $command->runProcess('php artisan inertia:middleware --ansi');

        $file = 'templates/app.' . (int) in_array(SentryModule::class, $command->modules) . '.js';
        $command->storage->publish($file, 'resources/js/app.js');
    }

    /**
     * Optional load additional modules wich not autoloaded.
     */
    public static function load(InstallLaravelCommand $command): array
    {
        $modules = [];

        $font = select(
            label: 'Install Font Awesome Vue?',
            options: [
                'no' => 'No',
                'free' => 'Yes, Font Awesome Free',
                'pro' => 'Yes, Font Awesome Pro',
            ],
            default: 'no'
        );

        if ($font == 'free') {
            $modules[] = FontAwesomeModule::class;
        } elseif ($font == 'pro') {
            $modules[] = FontAwesomeProModule::class;
        }

        if (confirm('Install ESLint?')) {
            $modules[] = ESLintModule::class;
        }

        if (confirm('Install Headless UI?')) {
            $modules[] = HeadlessUIModule::class;
        }

        return $modules;
    }
}
