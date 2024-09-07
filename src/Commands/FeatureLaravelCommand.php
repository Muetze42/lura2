<?php

namespace NormanHuth\Lura\Commands;

use NormanHuth\Lura\AbstractCommand;

class FeatureLaravelCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'features:install:laravel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Features in existing Laravel Project';
}
