<?php

namespace NormanHuth\Lura\Features\Laravel\Removers;

use NormanHuth\Lura\Commands\InstallLaravelCommand;
use NormanHuth\Lura\Contracts\AbstractFeature;
use NormanHuth\Lura\Support\Package;

class RemoveNunomaduroCollisionFeature extends AbstractFeature
{
    /**
     * Determine the name of the feature.
     */
    public static function name(): string
    {
        return 'Remove Collision from dev requirements';
    }

    /**
     * Determine composer dev requirements wich should be removed.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function removeComposerDevRequirement(InstallLaravelCommand $command): array
    {
        return [
            new Package('nunomaduro/collision'),
        ];
    }
}
