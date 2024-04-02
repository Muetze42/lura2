<?php

namespace NormanHuth\Luraa\Support;

use Illuminate\Support\Arr;
use NormanHuth\Luraa\Services\DependenciesFilesService;

class Package
{
    protected string $package;

    protected string $version;

    protected bool $forceVersion;

    public function __construct(string $package, string $version = '*', bool $forceVersion = false)
    {
        $this->package = $package;
        $this->version = preg_replace('/[^a-zA-Z0-9.^*@-]/', '', $version);
        $this->forceVersion = $forceVersion;
    }

    public function addComposerRequirement(DependenciesFilesService $service): void
    {
        $service->addComposerRequirement($this->package, $this->version, $this->forceVersion);
    }

    public function addComposerDevRequirement(DependenciesFilesService $service): void
    {
        $service->addComposerDevRequirement($this->package, $this->version, $this->forceVersion);
    }

    public function addPackageDependency(DependenciesFilesService $service): void
    {
        $service->addPackageDependency($this->package, $this->version, $this->forceVersion);
    }

    public function addPackageDevDependency(DependenciesFilesService $service): void
    {
        $service->addPackageDevDependency($this->package, $this->version, $this->forceVersion);
    }

    public function removeComposerRequirement(DependenciesFilesService $service): void
    {
        $service->removeComposerRequirement($this->package);
    }

    public function removeComposerDevRequirement(DependenciesFilesService $service): void
    {
        $service->removeComposerDevRequirement($this->package);
    }

    public function removePackageDependency(DependenciesFilesService $service): void
    {
        $service->removePackageDependency($this->package);
    }

    public function removePackageDevDependency(DependenciesFilesService $service): void
    {
        $service->removePackageDevDependency($this->package);
    }

    public static function methods(): array
    {
        return Arr::map(Arr::where(
            (new \ReflectionClass(static::class))->getMethods(\ReflectionMethod::IS_PUBLIC),
            fn (\ReflectionMethod $method) => !$method->isStatic() && $method->getName() != '__construct'
        ), fn (\ReflectionMethod $method) => $method->getName());
    }
}
