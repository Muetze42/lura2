<?php

namespace NormanHuth\Lura\Features\Laravel;

use NormanHuth\Lura\Commands\InstallLaravelCommand;
use NormanHuth\Lura\AbstractFeature;
use NormanHuth\Lura\Support\Package;

class LaravelNovaFeature extends AbstractFeature
{
    /**
     * Determine the name of the feature.
     */
    public static function name(): string
    {
        return 'Laravel Nova';
    }

    /**
     * Determine composer requirements for this feature.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function addComposerRequirement(InstallLaravelCommand $command): array
    {
        return [
            new Package('laravel/nova', '^4.35.1'),
            new Package('norman-huth/nova-assets-versioning', '^1.0'),
        ];
    }

    /**
     * Perform action after create project.
     */
    public static function afterCreateProject(InstallLaravelCommand $command): void
    {
        $command->env->addKeys('NOVA_LICENSE_KEY', 'APP_URL');
        $command->dependencies->addComposerRepository([
            'type' => 'composer',
            'url' => 'https://nova.laravel.com',
        ]);
        $command->storage->publish('stubs/nova', 'stubs/nova');
        $command->storage->publish('templates/nova/Commands', 'app/Console/Commands/Nova');
        static::determineLaravelNovaKey($command);
    }

    /**
     * Perform action after the composer install process.
     */
    public static function afterComposerInstall(InstallLaravelCommand $command): void
    {
        $command->runProcess('php artisan nova:install --ansi');
        $command->runProcess('php artisan vendor:publish --tag=nova-lang --ansi');
        $command->storage->publish('templates/NovaServiceProvider.php', 'app/Providers/NovaServiceProvider.php');
    }

    protected static function determineLaravelNovaKey(InstallLaravelCommand $command): void
    {
        $authJson = dirname($command->storage->packagePath(), 3) . DIRECTORY_SEPARATOR . 'auth.json';

        if (! file_exists($authJson)) {
            return;
        }
        $data = json_decode(file_get_contents($authJson), true);
        foreach (data_get($data, 'http-basic', []) as $target => $basicAuth) {
            if ($target != 'nova.laravel.com') {
                continue;
            }
            $command->env->setValue('NOVA_LICENSE_KEY', data_get($basicAuth, 'password'));
        }
    }
}
