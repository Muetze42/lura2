<?php

namespace NormanHuth\Lura\Features\Laravel;

use NormanHuth\Lura\Commands\InstallLaravelCommand;
use NormanHuth\Lura\Contracts\AbstractFeature;
use NormanHuth\Lura\Support\Package;
use NormanHuth\Prompts\Prompt;

use function Laravel\Prompts\confirm;

class InertiaJsFeature extends AbstractFeature
{
    /**
     * Determine the name of the feature.
     */
    public static function name(): string
    {
        return 'Inertia.js';
    }

    /**
     * Determine if this feature should be checked by default if autoloaded.
     */
    public static function default(): bool
    {
        return true;
    }

    /**
     * Determine composer requirements for this feature.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function addComposerRequirement(InstallLaravelCommand $command): array
    {
        return [
            new Package('inertiajs/inertia-laravel', '^1.0'),
        ];
    }

    /**
     * Determine Node package dependencies for this feature.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function addPackageDependency(InstallLaravelCommand $command): array
    {
        return [
            new Package('@inertiajs/vue3', '^1.0.15'),
            new Package('@vitejs/plugin-vue', '^5.0.4'),
            new Package('vue', '^3.4.24'),
        ];
    }

    /**
     * Perform action after create project.
     */
    public static function afterCreateProject(InstallLaravelCommand $command): void
    {
        $file = 'templates/vite.config.' . (int) in_array(SentryFeature::class, $command->features) . '.js';
        $command->storage->publish($file, 'vite.config.js');
    }

    /**
     * Perform action after the composer install process.
     */
    public static function afterComposerInstall(InstallLaravelCommand $command): void
    {
        $command->runProcess('php artisan inertia:middleware --ansi');

        $file = 'templates/app.' . (int) in_array(SentryFeature::class, $command->features) . '.js';
        $command->storage->publish($file, 'resources/js/app.js');
    }

    /**
     * Optional load additional features wich not autoloaded.
     */
    public static function load(InstallLaravelCommand $command): array
    {
        $features = [];

        $font = Prompt::select(
            label: 'Install Font Awesome Vue?',
            options: [
                'no' => 'No',
                'free' => 'Yes, Font Awesome Free',
                'pro' => 'Yes, Font Awesome Pro',
            ],
            default: 'no'
        );

        if ($font == 'free') {
            $features[] = FontAwesomeFeature::class;
        } elseif ($font == 'pro') {
            $features[] = FontAwesomeProFeature::class;
        }

        if (confirm('Install ESLint?')) {
            $features[] = ESLintFeature::class;
        }

        if (confirm('Install Headless UI?')) {
            $features[] = HeadlessUIFeature::class;
        }

        return $features;
    }
}
