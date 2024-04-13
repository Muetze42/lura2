<?php

namespace NormanHuth\Lura;

use Illuminate\Container\Container as BaseContainer;

class Container extends BaseContainer
{
    /**
     * Determine if the application is running unit tests.
     */
    public function runningUnitTests(): bool
    {
        return false;
    }
}
