<?php

namespace NormanHuth\Luraa;

use Illuminate\Console\Application;
use Illuminate\Events\Dispatcher;
use NormanHuth\Library\Support\ClassFinder;

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
        $this->artisan = new Application($this->container, $this->events, '1');

        $this->artisan->setDefaultCommand('install:laravel');
        $this->artisan->setName('Luraa');
        $this->resolveCommands();
        $this->artisan->setCatchExceptions(true);
        $this->artisan->run();
    }

    protected function resolveCommands(): void
    {
        collect(ClassFinder::load(
            paths: __DIR__ . '/Commands',
            namespace: 'NormanHuth\Luraa\Commands',
            basePath: __DIR__ . '/Commands'
        ))->each(fn ($command) => $this->artisan->resolve($command));
    }
}
