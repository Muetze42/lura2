<?php

namespace NormanHuth\Luraa\Commands;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use NormanHuth\Library\Support\ClassFinder;
use NormanHuth\Luraa\Contracts\FeatureInterface;
use NormanHuth\Luraa\Features\Laravel\InertiaJsFeature;
use NormanHuth\Luraa\Features\Laravel\SentryFeature;
use NormanHuth\Luraa\Services\DependenciesFilesService;
use NormanHuth\Luraa\Services\EnvFileService;
use NormanHuth\Luraa\Support\Package;
use NormanHuth\Prompts\Prompt;

use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\text;

class InstallLaravelCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:laravel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'A custom Laravel application installer';

    public string $appName = '';

    protected string $appPath;

    protected ?string $tempPath = null;

    public string $composer;

    public EnvFileService $env;

    public DependenciesFilesService $dependencies;

    protected string $defaultCacheStore = 'file';

    protected string $sessionDriver = 'database';

    protected string $defaultQueueConnection = 'database';

    /**
     * @var array<\NormanHuth\Luraa\Contracts\FeatureInterface|string>
     */
    public array $features = [];

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        intro('Creating a Laravel Project');

        $this->determineAppData();
        if (!$this->isTargetPathOk()) {
            return;
        }

        $this->processingActions();

        outro(
            sprintf(
                'Your project "%s" has been successfully created at "%s"',
                $this->appName,
                $this->storage->targetPath()
            )
        );
    }

    protected function processingActions(): void
    {
        $this->beforeCreateProject();
        $this->createProject();
        $this->afterCreateProject();
        $this->composerInstall();
        $this->afterComposerInstall();
        $this->moveTempContentBack();
    }

    protected function determineAppData(): void
    {
        $this->line('The folder name is determined in accordance with Git based on the name of the app.');
        $this->line('If the folder already exists, it may not contain any files or folders created by Laravel.');

        $this->appName = text(
            label: 'Name of the new app',
            default: $this->appName,
            required: true,
        );

        $this->appPath = Str::lower(trim(trim(preg_replace(['/[^A-Za-z0-9-_.]+/', '!-+!'], '-', $this->appName), '-')));

        $validated = $this->validate(
            data: ['name' => $this->appName],
            rules: ['name' => 'required|string|min:1|max:50']
        );

        if (empty($this->appPath)) {
            $this->error('Could not determine the app path with this name.');
        }

        $this->initializeStorage();

        if ($validated || empty($this->appPath)) {
            $this->determineAppData();
        }
    }

    protected function determineOptions(): void
    {
        $features = Arr::where(ClassFinder::load(
            paths: dirname(__DIR__) . '/Features/Laravel',
            subClassOf: FeatureInterface::class,
            namespace: 'NormanHuth\Luraa',
            basePath: dirname(__DIR__)
        ), fn (FeatureInterface|string $feature) => $feature::autoload());

        $options = Arr::mapWithKeys(
            $features,
            fn (FeatureInterface|string $feature) => [$feature => $feature::name()]
        );

        asort($options, SORT_NATURAL | SORT_FLAG_CASE);

        $default = Arr::where(
            $features,
            fn (FeatureInterface|string $feature) => $feature::default()
        );

        $this->features = Prompt::multiselect(
            label: 'Select optional features',
            options: $options,
            default: $default,
            scroll: count($options),
            hint: 'Selected features are applied during installation and prepared for use.'
        );

        $this->loadFeatures($this->features);
    }

    /**
     * @param array<\NormanHuth\Luraa\Contracts\FeatureInterface|string>  $features
     */
    protected function loadFeatures(array $features): void
    {
        if (empty($features)) {
            return;
        }

        $loaded = [];

        foreach ($features as $feature) {
            $loaded = array_merge($loaded, $feature::load($this));
        }

        if (empty($loaded)) {
            return;
        }

        $this->features = array_merge($this->features, $loaded);

        /* Load nested features */
        $this->loadFeatures($loaded);
    }

    protected function beforeCreateProject(): void
    {
        $this->composer = $this->findComposer();
        $this->determineOptions();
        foreach ($this->features as $feature) {
            $feature::beforeCreateProject($this);
        }
        $this->determineDefaultCacheStore();
        $this->determineDefaultQueueConnection();
        $this->determineSessionDriver();
    }

    protected function afterCreateProject(): void
    {
        $packageMethods = Package::methods();
        foreach ($this->features as $feature) {
            foreach ($packageMethods as $method) {
                foreach ($feature::{$method}($this) as $package) {
                    $package->{$method}($this->dependencies);
                }
            }
            foreach ($feature::composerScripts($this) as $key => $value) {
                $this->dependencies->addComposerScript($key, $value);
            }
            $feature::afterCreateProject($this);
        }

        $this->storage->publish('stubs/laravel', 'stubs');
    }

    protected function composerInstall(): void
    {
        $command = [
            $this->composer,
            'install',
            '--prefer-dist',
            '--ansi',
        ];

        $this->runProcess($command);
    }

    protected function afterComposerInstall(): void
    {
        $this->installerAfterComposerInstall();

        foreach ($this->features as $feature) {
            $feature::afterComposerInstall($this);
        }

        $this->processesAfterComposerInstall();
    }

    protected function installerAfterComposerInstall(): void
    {
        $this->setEnvVariables();
        $this->storage->publish('templates/css', 'resources/css');

        $this->abstractController();
        $this->serviceProvider();
        $this->bootstrapAppFile();
        $this->storage->publish('templates/.editorconfig');
        $this->storage->publish('templates/fonts', 'resources/fonts');
    }

    protected function processesAfterComposerInstall(): void
    {
        $this->runProcess('php artisan lang:publish --ansi');
        $this->runProcess('php artisan key:generate --ansi');

        // Finally
        if ($this->storage->targetDisk->exists('pint.json')) {
            $this->runProcess($this->composer . ' pint --ansi');
        }
    }

    protected function bootstrapAppFile(): void
    {
        $this->storage->publish('templates/api.php', 'routes/api.php');

        $file = sprintf(
            'templates/app.%d.%d.php',
            (int) in_array(InertiaJsFeature::class, $this->features),
            (int) in_array(SentryFeature::class, $this->features),
        );
        $this->storage->publish($file, 'bootstrap/app.php');
    }

    protected function abstractController(): void
    {
        $file = 'app/Http/Controllers/Controller.php';
        if ($this->storage->targetDisk->exists($file)) {
            $this->storage->targetDisk->delete($file);
        }
        $this->storage->publish('templates/AbstractController.php', 'app/Http/Controllers/AbstractController.php');
    }

    protected function serviceProvider(): void
    {
        $this->storage->publish('templates/AppServiceProvider.php', 'app/Providers/AppServiceProvider.php');
    }

    protected function setEnvVariables(): void
    {
        $variables = [
            'APP_NAME' => '"' . addslashes($this->appName) . '"',
            'LOG_STACK' => 'daily',
            'CACHE_STORE' => $this->defaultCacheStore,
            'SESSION_DRIVER' => $this->sessionDriver,
            'QUEUE_CONNECTION' => $this->defaultQueueConnection,
            'APP_URL' => 'http://localhost:8000',
        ];

        foreach ($variables as $key => $value) {
            $this->env->setValue($key, $value);
            $this->env->setExampleValue($key, $value);
        }
    }

    protected function determineDefaultCacheStore(): void
    {
        $this->defaultCacheStore = Prompt::select(
            label: 'Which cache store should be used as default?',
            options: ['database', 'file', 'redis', 'memcached', 'apc', 'array', 'dynamodb', 'octane', 'null'],
            default: $this->defaultCacheStore,
            hint: 'This connection is utilized if another isn\'t explicitly ' .
                'specified when running a cache operation inside the application.',
            required: true
        );
    }

    protected function determineDefaultQueueConnection(): void
    {
        $this->defaultQueueConnection = Prompt::select(
            label: 'Which queue connection should be used as default?',
            options: ['sync', 'database', 'redis', 'beanstalkd', 'sqs', 'null'],
            default: $this->defaultQueueConnection,
            required: true
        );
    }

    protected function determineSessionDriver(): void
    {
        $this->sessionDriver = Prompt::select(
            label: 'Which session driver should be used?',
            options: ['file', 'database', 'redis', 'cookie', 'apc', 'memcached', 'dynamodb', 'array'],
            default: $this->sessionDriver,
            hint: 'Database storage is a great default choice.',
            required: true
        );
    }

    protected function createProject(): void
    {
        $command = [
            $this->composer,
            'create-project laravel/laravel',
            $this->appPath,
            '--no-install',
            '--no-interaction',
            '--no-scripts',
            '--remove-vcs',
            '--prefer-dist',
            '--ansi',
        ];

        $this->runProcess($command, $this->storage->cwdPath());

        $this->dependencies = new DependenciesFilesService(
            packageJsonFile: $this->storage->targetDisk->path('package.json'),
            composerJsonFile: $this->storage->targetDisk->path('composer.json')
        );

        $this->env = new EnvFileService($this->storage->targetDisk);
    }

    protected function moveTempContentBack(): void
    {
        if (!$this->tempPath) {
            return;
        }

        foreach ($this->storage->cwdDisk->directories($this->tempPath) as $directory) {
            $this->storage->filesystem->moveDirectory(
                $this->storage->cwdDisk->path($directory),
                $this->storage->targetDisk->path(basename($directory)),
                true
            );
        }
        foreach ($this->storage->cwdDisk->files($this->tempPath) as $file) {
            $this->storage->filesystem->move(
                $this->storage->cwdDisk->path($file),
                $this->storage->targetDisk->path(basename($file))
            );
        }
        $this->storage->cwdDisk->deleteDirectory($this->tempPath);
    }

    protected function isTargetPathOk(): bool
    {
        $files = $this->storage->targetDisk->files();
        $directories = $this->storage->targetDisk->directories();

        if (!count($directories) && !count($files)) {
            return true;
        }

        $laravel = $this->storage->packageDisk->json('data/laravel-file-structure.json');

        $files = array_intersect($files, $laravel['files']);
        $directories = array_intersect($directories, $laravel['directories']);

        if (count($directories) || count($files)) {
            $this->components->error(
                sprintf(
                    'The target path "%s" already contains Laravel files or directories.',
                    $this->appPath
                )
            );

            return false;
        }

        $this->determineTempPath();
        $this->storage->filesystem->moveDirectory(
            $this->storage->targetDisk->path(''),
            $this->storage->cwdDisk->path($this->tempPath)
        );

        return true;
    }

    protected function determineTempPath(): void
    {
        $temp = 'temp-' . Str::random();
        if ($this->storage->cwdDisk->exists($temp)) {
            $this->determineTempPath();
        }
        $this->tempPath = $temp;
    }
}
