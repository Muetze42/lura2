<?php

namespace NormanHuth\Luraa\Contracts;

trait ComposerInstallTrait
{
    protected function composerInstall(): void
    {
        $this->beforeComposerInstall();
        $this->executeComposerInstall();
        $this->afterComposerInstall();
    }

    protected function beforeComposerInstall(): void
    {
        //
    }

    protected function executeComposerInstall(): void
    {
        //
    }

    protected function afterComposerInstall(): void
    {
        //
    }
}
