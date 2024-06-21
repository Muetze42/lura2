<?php

namespace NormanHuth\Lura;

use Composer\InstalledVersions;
use Illuminate\Console\Application;
use Illuminate\Console\Command;
use Illuminate\Events\Dispatcher;
use NormanHuth\Library\ClassFinder;
use NormanHuth\Lura\Support\Http;

use function Laravel\Prompts\spin;
use function Laravel\Prompts\alert;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\pause;

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
     * @throws \Exception
     */
    public function __construct()
    {
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
            fn () => Http::get('https://api.github.com/repos/Muetze42/lura2/commits?per_page=1'),
            'Checking for update...'
        );

        if ($response->failed()) {
            return;
        }

        $reference = $response->json('0.sha');

        if ($reference == InstalledVersions::getReference('norman-huth/lura2')) {
            return;
        }

        note('https://github.com/Muetze42/lura2');
        pause('A new version of Lura2 is available. Press ENTER to continue.');
    }

    protected function resolveCommands(): void
    {
        collect(ClassFinder::load(
            paths: __DIR__.'/Commands',
            subClassOf: Command::class
        ))->each(fn ($command) => $this->artisan->resolve($command));
    }
}
