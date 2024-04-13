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
        return 'ESLint';
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
