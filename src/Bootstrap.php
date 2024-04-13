<?php

namespace NormanHuth\Lura;

use Illuminate\Console\Application;
use Illuminate\Console\Command;
use Illuminate\Events\Dispatcher;
use NormanHuth\Library\ClassFinder;

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
        $this->container = new Container();
        $this->events = new Dispatcher($this->container);
        $this->artisan = new Application($this->container, $this->events, '2');

        $this->artisan->setDefaultCommand('app:install:laravel');
        $this->artisan->setName('Lura2');
        $this->resolveCommands();
        $this->artisan->setCatchExceptions(true);
        $this->artisan->run();
    }

    protected function resolveCommands(): void
    {
        collect(ClassFinder::load(
            paths: __DIR__ . '/Commands',
            subClassOf: Command::class
        ))->each(fn ($command) => $this->artisan->resolve($command));
    }
}
