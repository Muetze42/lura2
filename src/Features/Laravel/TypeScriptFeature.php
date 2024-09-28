<?php

namespace NormanHuth\Lura\Features\Laravel;

use NormanHuth\Lura\Commands\InstallLaravelCommand;
use NormanHuth\Lura\AbstractFeature;
use NormanHuth\Lura\Support\Package;

class TypeScriptFeature extends AbstractFeature
{
    /**
     * Determine the name of the feature.
     */
    public static function name(): string
    {
        return 'TypeScript';
    }

    /**
     * Determine if this feature should be autoloaded.
     */
    public static function autoload(): bool
    {
        return false;
    }

    /**
     * Determine Node package dependencies for this feature.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function addPackageDevDependency(InstallLaravelCommand $command): array
    {
        return [
            new Package('@rushstack/eslint-patch', '^1.10.4'),
            new Package('@types/node', '^22.7.4'),
            new Package('@typescript-eslint/eslint-plugin', '^8.4.0'),
            new Package('typescript', '^5.5.4'),
            new Package('vue-tsc', '^2.1.6'),
        ];
    }

    /**
     * Perform action after create project.
     */
    public static function afterCreateProject(InstallLaravelCommand $command): void
    {
        $command->storage->publish('templates/tsconfig.json');
        $command->storage->publish('templates/types', 'resources/js/types');
    }

    /**
     * Determine package scripts for this feature.
     *
     * @return array{string, string|array}
     */
    public static function packageScripts(InstallLaravelCommand $command): array
    {
        return ['build' => 'vue-tsc && vite build'];
    }
}
