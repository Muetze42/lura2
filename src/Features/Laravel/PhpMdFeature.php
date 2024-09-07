<?php

namespace NormanHuth\Lura\Features\Laravel;

use NormanHuth\Lura\Commands\InstallLaravelCommand;
use NormanHuth\Lura\AbstractFeature;
use NormanHuth\Lura\Support\ComposerScript;
use NormanHuth\Lura\Support\Package;

class PhpMdFeature extends AbstractFeature
{
    /**
     * Determine the name of the feature.
     */
    public static function name(): string
    {
        return '[Code Quality] PHPMD - PHP Mess Detector';
    }

    /**
     * Determine if this feature should be checked by default if autoloaded.
     */
    public static function default(): bool
    {
        return true;
    }

    /**
     * Determine composer dev requirements for this feature.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function addComposerDevRequirement(InstallLaravelCommand $command): array
    {
        return [
            new Package('phpmd/phpmd', '^2.15'),
        ];
    }

    /**
     * Determine composer scripts for this feature.
     *
     * @return array{string, string|array}
     */
    public static function composerScripts(InstallLaravelCommand $command): array
    {
        return [
            new ComposerScript(
                'phpmd',
                './vendor/bin/phpmd app,bootstrap,config,database,routes ansi phpmd.xml',
                'Look for several potential problems within the source'
            ),
        ];
    }
}
