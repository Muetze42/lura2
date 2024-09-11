<?php

namespace NormanHuth\Lura\Features\Laravel;

use NormanHuth\Lura\Commands\InstallLaravelCommand;
use NormanHuth\Lura\AbstractFeature;
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
        return false;
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
        $file = 'templates/vite.config.' . (int) in_array(SentryFeature::class, $command->features);
        $file .= in_array(TypeScriptFeature::class, $command->features) ? '.ts' : '.js';
        $command->storage->publish($file, 'vite.config.js');
        $command->storage->publish('templates/app.blade.php', 'resources/views/app.blade.php');
    }

    /**
     * Perform action after the composer install process.
     */
    public static function afterComposerInstall(InstallLaravelCommand $command): void
    {
        $command->runProcess('php artisan inertia:middleware --ansi');

        $ext = in_array(TypeScriptFeature::class, $command->features) ? '.ts' : '.js';
        $file = 'templates/app.' . (int) in_array(SentryFeature::class, $command->features) . $ext;
        $command->storage->publish($file, 'resources/js/app.js');
    }

    /**
     * Optional load additional features wich not autoloaded.
     */
    public static function load(InstallLaravelCommand $command): array
    {
        $features = [];

        $packages = Prompt::multiselect(
            label: 'Select additional dependencies:',
            options: [
                'eslint' => 'ESLint',
                'headless' => 'Headless UI',
                'typescript' => 'TypeScript',
                'primevue' => 'PrimeVue',
            ],
            default: ['eslint', 'typescript']
        );

        if (in_array('eslint', $packages)) {
            $features[] = ESLintFeature::class;
        }

        if (in_array('headless', $packages)) {
            $features[] = HeadlessUIFeature::class;
        }

        if (in_array('typescript', $packages)) {
            $features[] = TypeScriptFeature::class;
        }

        if (in_array('primevue', $packages)) {
            $features[] = PrimeVueFeature::class;
        }

        $font = Prompt::select(
            label: 'Install Vue Icons?',
            options: [
                'no' => 'No',
                'fa-free' => 'Font Awesome Free',
                'fa-pro' => 'Font Awesome Pro',
                'heroicons' => 'Heroicons',
                'primeicons' => 'PrimeIcons',
                'vue-material-design-icons' => 'Vue Material Design Icons',
            ],
            default: 'no',
            scroll: 10
        );

        if ($font == 'fa-free') {
            $features[] = FontAwesomeFeature::class;

            return $features;
        }

        if ($font == 'fa-pro') {
            $features[] = FontAwesomeProFeature::class;

            return $features;
        }

        if ($font == 'heroicons') {
            $features[] = HeroiconsFeature::class;

            return $features;
        }

        if ($font == 'primeicons') {
            $features[] = PrimeIconsFeature::class;

            return $features;
        }

        if ($font == 'vue-material-design-icons') {
            $features[] = VueMaterialDesignIconsFeature::class;

            return $features;
        }

        return $features;
    }
}
