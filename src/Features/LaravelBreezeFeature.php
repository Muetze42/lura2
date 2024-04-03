<?php

namespace NormanHuth\Luraa\Features;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractFeature;
use NormanHuth\Luraa\Support\Package;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\select;

/**
 * Todo: Testing.
 */
class LaravelBreezeFeature extends AbstractFeature
{
    protected static string $stack;

    protected static array $features = [];

    protected static bool $dark = false;

    protected static string $pest;

    /**
     * Determine the name of the feature.
     */
    public static function name(): string
    {
        return 'Laravel Breeze';
    }

    /**
     * Determine composer dev requirements for this feature.
     *
     * @return array<\NormanHuth\Luraa\Support\Package>
     */
    public static function addComposerDevRequirement(InstallLaravelCommand $command): array
    {
        return [
            new Package('laravel/breeze', '^2.0'),
        ];
    }

    /**
     * Perform action after the composer install process.
     */
    public static function afterComposerInstall(InstallLaravelCommand $command): void
    {
        $artisan = ['php artisan breeze:install ' . static::$stack];
        if (static::$dark || in_array('dark', static::$features)) {
            $artisan[] = '--dark';
        }
        if (in_array('ssr', static::$features)) {
            $artisan[] = '--ssr';
        }
        if (in_array('typescript', static::$features)) {
            $artisan[] = '--typescript';
        }

        $artisan[] = ' --ansi';

        $command->runProcess(implode(' ', $artisan));
    }

    /**
     * Perform action before create project.
     */
    public static function beforeCreateProject(InstallLaravelCommand $command): void
    {
        static::$stack = select(
            label: 'Which Breeze stack would you like to install?',
            options: [
                'blade' => 'Blade with Alpine',
                'livewire' => 'Livewire (Volt Class API) with Alpine',
                'livewire-functional' => 'Livewire (Volt Functional API) with Alpine',
                'react' => 'React with Inertia',
                'vue' => 'Vue with Inertia',
                'api' => 'API only',
            ],
            scroll: 6,
        );

        if (in_array(static::$stack, ['react', 'vue'])) {
            static::$features = multiselect(
                label: 'Would you like any optional features?',
                options: [
                    'dark' => 'Dark mode',
                    'ssr' => 'Inertia SSR',
                    'typescript' => 'TypeScript',
                ]
            );
        } elseif (in_array(static::$stack, ['blade', 'livewire', 'livewire-functional'])) {
            static::$dark = confirm(
                label: 'Would you like dark mode support?',
                default: false
            );
        }

        static::$pest = select(
            label: 'Which testing framework do you prefer?',
            options: ['Pest', 'PHPUnit'],
            default: in_array(PestPluginFeature::class, $command->features) ? 'Pest' : 'PHPUnit',
        ) === 'Pest';
    }
}
