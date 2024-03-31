<?php

namespace NormanHuth\Luraa\Support;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Filesystem\Filesystem as FilesystemInterface;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;
use NormanHuth\Luraa\Container;

class Storage
{
    /**
     * The filesystem manager instance.
     */
    protected FilesystemManager $filesystemManager;

    /**
     * The Filesystem instance.
     */
    public Filesystem $filesystem;

    public function __construct(string $targetPath, string $packagePath)
    {
        $this->registerManager($targetPath, $packagePath);
        $this->filesystem = new Filesystem();
    }

    /**
     * Get the package disk instance.
     */
    public function packageDisk(): FilesystemInterface
    {
        return $this->filesystemManager->disk('package');
    }

    /**
     * Get the target disk instance.
     */
    public function targetDisk(): FilesystemInterface
    {
        return $this->filesystemManager->disk('target');
    }

    /**
     * Get the cwd disk instance.
     */
    public function cwdDisk(): FilesystemInterface
    {
        return $this->filesystemManager->disk('cwd');
    }

    /**
     * Get the full path to the package.
     */
    public function packagePath(): string
    {
        return $this->packageDisk()->path('');
    }

    /**
     * Get the full path to the target.
     */
    public function targetPath(): string
    {
        return $this->targetDisk()->path('');
    }

    /**
     * Get the full path to the cwd.
     */
    public function cwdPath(): string
    {
        return $this->cwdDisk()->path('');
    }

    public function publish(string $from, string $to): void
    {
        if (is_dir($this->packageDisk()->path($from))) {
            $this->filesystem->copyDirectory(
                $this->packageDisk()->path($from),
                $this->targetDisk()->path($to),
            );

            return;
        }

        $this->filesystem->copy(
            $this->packageDisk()->path($from),
            $this->targetDisk()->path($to),
        );
    }

    /**
     * Register the filesystem manager.
     */
    protected function registerManager(string $targetPath, string $packagePath): void
    {
        $container = new Container();
        $container->instance('app', $container);
        $container['config'] = new Repository([
            'filesystems' => [
                'default' => 'package',
                'disks' => [
                    'package' => [
                        'driver' => 'local',
                        'root' => $packagePath,
                    ],
                    'target' => [
                        'driver' => 'local',
                        'root' => $targetPath,
                    ],
                    'cwd' => [
                        'driver' => 'local',
                        'root' => getcwd(),
                    ],
                ],
            ],
        ]);
        /** @var \Illuminate\Contracts\Foundation\Application $container */
        $this->filesystemManager = new FilesystemManager($container);
    }
}
