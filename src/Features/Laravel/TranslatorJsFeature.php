<?php

namespace NormanHuth\Lura\Features\Laravel;

use NormanHuth\Lura\Commands\InstallLaravelCommand;
use NormanHuth\Lura\Contracts\AbstractFeature;
use NormanHuth\Lura\Support\Package;

class TranslatorJsFeature extends AbstractFeature
{
    /**
     * Determine the name of the feature.
     */
    public static function name(): string
    {
        return 'TranslatorJS';
    }

    /**
     * Determine Node package dependencies for this feature.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function addPackageDependency(InstallLaravelCommand $command): array
    {
        return [
            new Package('@norman-huth/translator-js', '^1.1.0'),
        ];
    }
}
