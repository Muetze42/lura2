<?php

namespace NormanHuth\Lura\Features\Laravel;

use NormanHuth\Lura\Commands\InstallLaravelCommand;
use NormanHuth\Lura\Contracts\AbstractFeature;
use NormanHuth\Lura\Support\Package;

class ESLintFeature extends AbstractFeature
{
    /**
     * Determine the name of the feature.
     */
    public static function name(): string
    {
        return ' ESLint';
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
     * @return array{string: 'package' => string: 'version'}
     */
    public static function addPackageDevDependency(InstallLaravelCommand $command): array
    {
        $packages = [
            new Package('@babel/plugin-syntax-dynamic-import', '^7.8.3'),
            new Package('@vue/eslint-config-prettier', '^9.0.0'),
            new Package('eslint-plugin-vue', '^9.25.0'),
            new Package('@rushstack/eslint-patch', '^1.10.1'),
        ];

        if (in_array(TypeScriptFeature::class, $command->features)) {
            $packages[] = new Package('@typescript-eslint/eslint-plugin', '^8.3.0');
        }

        return $packages;
    }

    /**
     * Perform action after create project.
     */
    public static function afterCreateProject(InstallLaravelCommand $command): void
    {
        $command->storage->publish('templates/eslint');

        if (in_array(TypeScriptFeature::class, $command->features)) {
            $command->storage->publish('templates/.eslintrc.ts.cjs', '.eslintrc.cjs');
        }
    }

    /**
     * Determine package scripts for this feature.
     *
     * @return array{string, string|array}
     */
    public static function packageScripts(InstallLaravelCommand $command): array
    {
        return [
            'lint' => 'eslint resources/js --ext .vue,.js,.jsx,.cjs,.mjs,.ts,.tsx,.cts,.mts --fix --ignore-path .gitignore --ignore-pattern ziggy.* --ignore-pattern vuex.*',
            'format' => 'prettier --write src/',
        ];
    }
}
