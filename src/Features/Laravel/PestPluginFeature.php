<?php

namespace NormanHuth\Lura\Features\Laravel;

use NormanHuth\Lura\Commands\InstallLaravelCommand;
use NormanHuth\Lura\Contracts\AbstractFeature;
use NormanHuth\Lura\Support\Package;

class PestPluginFeature extends AbstractFeature
{
    /**
     * Determine the name of the feature.
     */
    public static function name(): string
    {
        return 'pestphp/pest-plugin-laravel';
    }

    /**
     * Determine composer dev requirements wich should be removed.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function addComposerDevRequirement(InstallLaravelCommand $command): array
    {
        return [
            new Package('pestphp/pest-plugin-laravel', '^2.3'),
        ];
    }
}
