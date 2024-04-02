<?php

namespace NormanHuth\Luraa\Modules;

use NormanHuth\Luraa\Commands\InstallLaravelCommand;
use NormanHuth\Luraa\Contracts\AbstractModule;

class PhpLibraryModule extends AbstractModule
{
    /**
     * Determine the name of the module.
     */
    public static function name(): string
    {
        return 'norman-huth/php-library';
    }

    /**
     * Determine composer requirements for this module.
     *
     * @return array{string: 'package', string: 'version'}
     */
    public static function composerRequirements(InstallLaravelCommand $command): array
    {
        return ['norman-huth/php-library' => '^0.0.2'];
    }

    /**
     * Determine if this module should be checked by default if autoloaded.
     */
    public static function default(): bool
    {
        return true;
    }

    /**
     * Perform action after the composer install process.
     */
    public static function afterComposerInstall(InstallLaravelCommand $command): void
    {
        $contents = $command->storage->targetDisk->get('bootstrap/app.php');
        $contents = str_replace(
            'use Illuminate\Http\Request;',
            'use Illuminate\Http\Request;' . "\n" . 'use NormanHuth\Library\Lib\CommandRegistry;',
            $contents
        );
        $contents = str_replace(
            '->withCommands()',
            '->withCommands(CommandRegistry::devCommands())',
            $contents
        );

        $command->storage->targetDisk->put('bootstrap/app.php', $contents);

        if (in_array(SentryModule::class, $command->modules)) {
            $contents = trim($command->storage->targetDisk->get('routes/api.php'));
            $contents = str_replace(
                'use Illuminate\Support\Facades\Route;',
                'use Illuminate\Support\Facades\Route;' . "\n" .
                'use NormanHuth\Library\Http\Controllers\Api\SentryTunnelController;',
                $contents
            );
            $contents .= "\n";
            $contents .= 'Route::post(\'sentry-tunnel\', SentryTunnelController::class);';

            $command->storage->targetDisk->put('routes/api.php', $contents . "\n");
        }
    }
}
