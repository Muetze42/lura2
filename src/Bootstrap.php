<?php

namespace NormanHuth\Lura;

use Composer\InstalledVersions;
use Illuminate\Console\Application;
use Illuminate\Console\Command;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Str;
use NormanHuth\Library\ClassFinder;
use NormanHuth\Library\Lib\MacroRegistry;
use NormanHuth\Library\Support\Macros\Str\SplitNewLinesMacro;
use NormanHuth\Lura\Support\Http;

use function Laravel\Prompts\pause;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\warning;

use const PHP_EOL;

class Bootstrap
{
    /**
     * The Application Instance.
     */
    protected Application $artisan;

    /**
     * The Container Instance.
     */
    protected Container $container;

    /**
     * The Dispatcher Instance.
     */
    protected Dispatcher $events;

    /**
     * Maximum number of change log items to display.
     */
    protected int $maxChangeLogItems = 20;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        MacroRegistry::macros([SplitNewLinesMacro::class => Str::class]);

        $this->checkForUpdate();

        $this->container = new Container();
        $this->events = new Dispatcher($this->container);
        $this->artisan = new Application($this->container, $this->events, '2');

        $this->artisan->setDefaultCommand('app:install:laravel');
        $this->artisan->setName('Lura2');
        $this->resolveCommands();
        $this->artisan->setCatchExceptions(true);
        $this->artisan->run();
    }

    protected function checkForUpdate(): void
    {
        $response = spin(
            fn () => Http::get(sprintf(
                'https://api.github.com/repos/Muetze42/lura2/commits?per_page=%d',
                $this->maxChangeLogItems
            )),
            'Checking for update...'
        );

        if ($response->failed()) {
            warning('Could not check for update');

            return;
        }

        $reference = InstalledVersions::getReference('norman-huth/lura2');
        $commits = $response->json();

        $changes = [];
        foreach ($commits as $commit) {
            if ($commit['sha'] == $reference) {
                break;
            }
            $message = trim(Str::splitNewLines($commit['commit']['message'])[0]);
            if (in_array(strtolower(explode(' ', $message)[0]), ['merge', 'style'])) {
                continue;
            }
            $changes[] = '• ' . $message;
        }

        if (! count($changes)) {
            return;
        }

        echo PHP_EOL;
        echo sprintf('Latest Changes (max %d) [https://github.com/Muetze42/lura2.git]:', $this->maxChangeLogItems);
        echo PHP_EOL;
        echo implode(PHP_EOL, $changes) . PHP_EOL;

        $notice = 'A new version of Lura2 is available. Press ENTER to continue.';
        if (windows_os()) {
            echo PHP_EOL;
            echo str_repeat('#', strlen($notice) + 4) . PHP_EOL;
            echo '#' . str_repeat(' ', strlen($notice) + 2) . '#' . PHP_EOL;
            echo '# ' . $notice . ' #' . PHP_EOL;
            echo '#' . str_repeat(' ', strlen($notice) + 2) . '#' . PHP_EOL;
            echo str_repeat('#', strlen($notice) + 4) . PHP_EOL;

            return;
        }
        pause($notice);
    }

    protected function resolveCommands(): void
    {
        collect(ClassFinder::load(
            paths: __DIR__ . '/Commands',
            subClassOf: Command::class
        ))->each(fn ($command) => $this->artisan->resolve($command));
    }
}
