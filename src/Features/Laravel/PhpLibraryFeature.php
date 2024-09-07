<?php

namespace NormanHuth\Lura\Features\Laravel;

use NormanHuth\Lura\Commands\InstallLaravelCommand;
use NormanHuth\Lura\AbstractFeature;
use NormanHuth\Lura\Support\Package;

class PhpLibraryFeature extends AbstractFeature
{
    /**
     * Determine the name of the feature.
     */
    public static function name(): string
    {
        return 'norman-huth/php-library';
    }

    /**
     * Determine composer requirements for this feature.
     *
     * @return array<\NormanHuth\Lura\Support\Package>
     */
    public static function addComposerRequirement(InstallLaravelCommand $command): array
    {
        return [
            new Package('norman-huth/php-library', '^2.8'),
        ];
    }

    /**
     * Determine if this feature should be checked by default if autoloaded.
     */
    public static function default(): bool
    {
        return false;
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

        if (in_array(SentryFeature::class, $command->features)) {
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
