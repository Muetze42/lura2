<?php

namespace NormanHuth\Luraa\Contracts;

trait CreateProjectTrait
{
    protected function createProject(): void
    {
        $this->beforeCreateProject();
        $this->executeCreateProject();
        $this->afterCreateProject();
    }

    protected function beforeCreateProject(): void
    {
        //
    }

    protected function executeCreateProject(): void
    {
        //
    }

    protected function afterCreateProject(): void
    {
        //
    }
}
