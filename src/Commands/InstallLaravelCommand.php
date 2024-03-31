<?php

namespace NormanHuth\Luraa\Commands;

use Illuminate\Support\Str;
use NormanHuth\Luraa\Services\DependenciesFilesService;
use NormanHuth\Luraa\Services\EnvFileService;
use NormanHuth\Luraa\Support\Storage;
use ReflectionClass;

use function Laravel\Prompts\intro;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;
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

    /**
     * The Storage instance.
     */
    protected Storage $storage;

    protected string $appName = '';

    protected string $appPath;

    protected ?string $tempPath = null;

    protected string $composer;

    protected EnvFileService $env;

    protected DependenciesFilesService $dependencies;

    protected array $options = [
        'Inertia.js' => true,
        'Laravel Nova' => false,
        'barryvdh/laravel-ide-helper' => true,
        'norman-huth/php-library' => true,
        'Sentry' => true,
        'Tailwind CSS' => true,
        'SCSS instead of CSS' => true,
        'HeadlessUI Vue' => true,
        'ESLint' => true,
        'spatie/laravel-activitylog' => false,
        'spatie/laravel-medialibrary' => false,
        'Custom Error Pages' => true,
        'Laravel Pint' => true,
        'Laravel Dusk' => false,
        'Laravel Sanctum' => false,
    ];

    protected string $optionFontAwesome = 'no';

    protected string $cacheStore = 'file';

    protected string $sessionDriver = 'database';

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

        $this->configureProject();

        spin(
            fn () => $this->initializeInstallerResources(),
            'Initializing installer resources'
        );

        $this->createProject();

        $this->composerInstall();

        $this->moveTempContentBack();

        outro(
            sprintf(
                'Your project "%s" has been successfully created at "%s"',
                $this->appName,
                $this->storage->targetPath()
            )
        );
    }

    protected function configureProject(): void
    {
        $this->determineOptions();
        $this->cacheStore();
        $this->sessionDriver();
        $this->determineFontAwesome();
    }

    protected function cacheStore(): void
    {
        $this->cacheStore = select(
            label: 'Which cache store should be used?',
            options: ['database', 'file', 'redis', 'memcached', 'apc', 'array', 'dynamodb', 'octane', 'null'],
            default: $this->cacheStore,
            hint: 'This connection is utilized if another isn\'t explicitly ' .
                'specified when running a cache operation inside the application.',
            required: true
        );
    }

    protected function sessionDriver(): void
    {
        $this->sessionDriver = select(
            label: 'Which session driver should be used?',
            options: ['file', 'database', 'redis', 'cookie', 'apc', 'memcached', 'dynamodb', 'array'],
            default: $this->sessionDriver,
            hint: 'Database storage is a great default choice.',
            required: true
        );
    }

    protected function determineFontAwesome(): void
    {
        if (!in_array('Inertia.js', $this->options)) {
            return;
        }
        $this->optionFontAwesome = select(
            label: 'Install Font Awesome Vue?',
            options: [
                'no' => 'No',
                'free' => 'Yes, Font Awesome Free',
                'pro' => 'Yes, Font Awesome Pro',
            ],
            default: $this->optionFontAwesome
        );
    }

    protected function determineOptions(): void
    {
        $options = [];
        if ($this->promptsUnsupportedEnvironment) {
            foreach ($this->options as $key => $value) {
                if ($this->confirm($key, $value)) {
                    $options[] = $key;
                }
            }

            $this->options = $options;

            return;
        }

        $this->options = multiselect(
            label: 'Select optional features to install',
            options: array_keys($this->options),
            default: array_keys(array_filter($this->options)),
            scroll: count($this->options),
        );
    }

    protected function createProject(): void
    {
        $this->beforeCreateProject();
        $this->executeCreateProject();
        $this->afterCreateProject();
    }

    protected function beforeCreateProject(): void
    {
        //
    }

    protected function executeCreateProject(): void
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
    }

    protected function afterCreateProject(): void
    {
        $this->dependencies = new DependenciesFilesService(
            packageJsonFile: $this->storage->targetDisk->path('package.json'),
            composerJsonFile: $this->storage->targetDisk->path('composer.json')
        );
        $this->env = new EnvFileService($this->storage->targetDisk);
    }

    protected function composerInstall(): void
    {
        $this->beforeComposerInstall();
        $this->executeComposerInstall();
        $this->afterComposerInstall();
    }

    protected function beforeComposerInstall(): void
    {
        $this->env->setValue('APP_NAME', '"' . addslashes($this->appName) . '"');
        $this->env->setExampleValue('APP_NAME', '"' . addslashes($this->appName) . '"');

        $this->env->setValue('LOG_STACK', 'daily');
        $this->env->setExampleValue('LOG_STACK', 'daily');

        $this->env->setValue('CACHE_STORE', $this->cacheStore);
        $this->env->setExampleValue('CACHE_STORE', $this->cacheStore);

        $this->env->setValue('SESSION_DRIVER', $this->sessionDriver);
        $this->env->setExampleValue('SESSION_DRIVER', $this->sessionDriver);

        if (in_array('Sentry', $this->options)) {
            $this->dependencies->addComposerRequirement('sentry/sentry-laravel', '^4.4');
        }

        if (in_array('Tailwind CSS', $this->options)) {
            $this->dependencies->addPackageDependency('tailwindcss', '^3.4.3');
            $this->dependencies->addPackageDependency('postcss', '^8.4.38');
            $this->dependencies->addPackageDependency('autoprefixer', '^10.4.19');
            $this->dependencies->addPackageDependency('@tailwindcss/forms', '^0.5.7');
            $this->dependencies->addPackageDependency('tailwind-scrollbar', '^3.0.5');
        }

        if (in_array('barryvdh/laravel-ide-helper', $this->options)) {
            $this->dependencies->addComposerDevRequirement('barryvdh/laravel-ide-helper', '^3.0"');
        }

        if (in_array('Laravel Pint', $this->options)) {
            $this->dependencies->addComposerDevRequirement('laravel/pint', '^1.15');
            $this->storage->publish('templates/pint.json');
            $this->dependencies->addComposerScript('pint', './vendor/bin/pint');
        }

        if (in_array('Laravel Dusk', $this->options)) {
            $this->dependencies->addComposerDevRequirement('laravel/dusk', '^8.1');
        }

        if (in_array('Inertia.js', $this->options)) {
            $this->dependencies->addComposerRequirement('inertiajs/inertia-laravel', '^1.0');
            $this->dependencies->addPackageDependency('@inertiajs/vue3', '^1.0.15');

            $file = 'templates/vite.config.' . (int) in_array('Sentry', $this->options) . '.js';
            $this->storage->publish($file, 'vite.config.js');
        }

        if ($this->optionFontAwesome != 'no') {
            $this->dependencies->addPackageDependency('@fortawesome/vue-fontawesome', '^3.0.6');
            $this->dependencies->addPackageDependency('@fortawesome/fontawesome-svg-core', '^6.5.1');
            $this->dependencies->addPackageDependency('@fortawesome/free-brands-svg-icons', '^6.5.1');
        }
        if ($this->optionFontAwesome == 'pro') {
            collect([
                'pro-duotone-svg-icons',
                'pro-light-svg-icons',
                'pro-regular-svg-icons',
                'pro-solid-svg-icons',
            ])->each(function ($package) {
                $this->dependencies->addPackageDependency('@fortawesome/' . $package, '^6.5.1');
            });
        }
        if ($this->optionFontAwesome == 'free') {
            collect(['free-regular-svg-icons', 'free-solid-svg-icons'])->each(function ($package) {
                $this->dependencies->addPackageDependency('@fortawesome/' . $package, '^6.5.1');
            });
        }

        $this->storage->publish('stubs/laravel', 'stubs');

        if (in_array('Laravel Nova', $this->options)) {
            $this->dependencies->addComposerRepository([
                'type' => 'composer',
                'url' => 'https://nova.laravel.com',
            ]);
            $this->dependencies->addComposerRequirement('laravel/nova', '^4.33');
            $this->dependencies->addComposerRequirement('norman-huth/nova-assets-versioning', '^1.0');
            $this->storage->publish('stubs/nova', 'stubs/nova');
            $this->storage->publish('resources/nova/Commands', 'app/Console/Commands/Nova');
        }

        if (in_array('spatie/laravel-medialibrary', $this->options)) {
            $this->dependencies->addComposerRequirement('spatie/laravel-medialibrary', '^11.4');
            $file = 'templates/media-library/config.' .
                (int) in_array('norman-huth/php-library', $this->options) . '.stub';
            $this->storage->publish($file, 'config/media-library.php');
            $this->storage->publish('templates/media-library/model.stub', 'app/Models/Media.php');
        }

        if (in_array('spatie/laravel-activitylog', $this->options)) {
            $this->dependencies->addComposerRequirement('spatie/laravel-activitylog', '^4.8');
            $this->storage->publish('templates/activity-log/model.stub', 'app/Models/Activity.php');
            $this->storage->publish('templates/activity-log/config.stub', 'config/activitylog.php');
            $this->storage->publish(
                'templates/activity-log/migration.stub',
                'database/migrations/' . $this->getMigrationPrefixedFileName('CreateActivityLogTable')
            );
        }

        if (in_array('norman-huth/php-library', $this->options)) {
            $this->dependencies->addComposerRequirement('norman-huth/php-library', '^0.0.2');
        }

        if (in_array('Sentry', $this->options)) {
            $this->env->addKeys([
                'SENTRY_LARAVEL_DSN',
                'SENTRY_TRACES_SAMPLE_RATE',
                'VITE_SENTRY_DSN_PUBLIC',
                'SENTRY_AUTH_TOKEN',
                'VITE_SENTRY_AUTH_TOKEN',
            ], 'APP_URL');
            $this->env->setValue('VITE_SENTRY_DSN_PUBLIC', '"${SENTRY_LARAVEL_DSN}"');
            $this->env->setExampleValue('VITE_SENTRY_DSN_PUBLIC', '"${SENTRY_LARAVEL_DSN}"');
            if (in_array('Sentry', $this->options)) {
                $this->dependencies->addPackageDependency('@sentry/vite-plugin', '^2.16.0');
                $this->dependencies->addPackageDependency('@sentry/vue', '^7.109.0');
            }
        }

        if (in_array('Laravel Sanctum', $this->options)) {
            $this->dependencies->addComposerRequirement('laravel/sanctum', '^4.0');
            $this->env->addKeys('SANCTUM_TOKEN_PREFIX', 'APP_URL');
        }

        if (in_array('HeadlessUI Vue', $this->options)) {
            $this->dependencies->addPackageDependency('@headlessui/vue', '^1.7.19');
        }

        if (in_array('ESLint', $this->options)) {
            $this->dependencies->addPackageDevDependency('@babel/plugin-syntax-dynamic-import', '^7.8.3');
            $this->dependencies->addPackageDevDependency('@vue/eslint-config-prettier', '^9.0.0');
            $this->dependencies->addPackageDevDependency('eslint-plugin-vue', '^9.24.0');
            $this->dependencies->addPackageDevDependency('@rushstack/eslint-patch', '^1.10.1');
        }

        $this->env->setValue('APP_URL', 'http://localhost:8000');
        $this->env->setExampleValue('VITE_SENTRY_DSN_PUBLIC', 'http://localhost:8000');

        $this->dependencies->close();
    }

    protected function executeComposerInstall(): void
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
        $this->runProcess('php artisan lang:publish --ansi');
        $this->runProcess('php artisan key:generate --ansi');

        if (in_array('Laravel Sanctum', $this->options)) {
            $this->runProcess('php artisan vendor:publish --tag=sanctum-migrations --ansi');
            $this->runProcess('php artisan vendor:publish --tag=sanctum-config --ansi');
        }

        $this->afterComposerInstallAbstractController();
        $this->afterComposerInstallServiceProvider();
        $this->afterComposerInstallInertia();
        $this->afterComposerInstallBootstrapApp();
        $this->afterComposerInstallStylesheet();
        $this->storage->publish('templates/.editorconfig');

        if (in_array('ESLint', $this->options)) {
            $this->storage->publish('templates/eslint');
        }

        if (in_array('Laravel Dusk', $this->options)) {
            $this->runProcess('php artisan dusk:install --ansi');
        }

        if (in_array('Laravel Nova', $this->options)) {
            $this->runProcess('php artisan nova:install --ansi');
            $this->storage->publish('templates/NovaServiceProvider.php', 'app/Providers/NovaServiceProvider.php');
        }

        if (in_array('Laravel Pint', $this->options)) {
            $this->runProcess($this->composer . ' pint --ansi');
        }
    }

    protected function afterComposerInstallAbstractController(): void
    {
        $file = 'app/Http/Controllers/Controller.php';
        if (!$this->storage->targetDisk->exists($file)) {
            return;
        }
        file_put_contents(
            $this->storage->targetDisk->path('app/Http/Controllers/AbstractController.php'),
            str_replace(
                'abstract class Controller',
                'abstract class AbstractController',
                $this->storage->targetDisk->get($file)
            )
        );
        $this->storage->targetDisk->delete($file);
    }

    protected function afterComposerInstallStylesheet(): void
    {
        $this->storage->publish('templates/fonts', 'resources/fonts');

        if (in_array('Tailwind CSS', $this->options)) {
            $this->storage->publish('templates/tailwind.config.js');
            $this->storage->publish('templates/postcss.config.js');
        }

        if (!in_array('SCSS instead of CSS', $this->options)) {
            $this->storage->publish('templates/css', 'resources/css');

            return;
        }
        $this->storage->publish('templates/scss', 'resources/scss');
        $this->storage->targetDisk->deleteDirectory('resources/css');
    }

    protected function afterComposerInstallServiceProvider(): void
    {
        $this->storage->publish('templates/AppServiceProvider.php', 'app/Providers/AppServiceProvider.php');
    }

    protected function afterComposerInstallBootstrapApp(): void
    {
        $this->storage->publish('templates/api.php', 'routes/api.php');

        $file = sprintf(
            'templates/app.%d.%d.php',
            (int) in_array('Inertia.js', $this->options),
            (int) in_array('Sentry', $this->options),
        );
        $this->storage->publish($file, 'bootstrap/app.php');

        if (in_array('norman-huth/php-library', $this->options)) {
            $contents = $this->storage->targetDisk->get('bootstrap/app.php');
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

            $this->storage->targetDisk->put('bootstrap/app.php', $contents);

            if (in_array('Sentry', $this->options)) {
                $contents = trim($this->storage->targetDisk->get('routes/api.php'));
                $contents = str_replace(
                    'use Illuminate\Support\Facades\Route;',
                    'use Illuminate\Support\Facades\Route;' . "\n" .
                        'use NormanHuth\Library\Http\Controllers\Api\SentryTunnelController;',
                    $contents
                );
                $contents .= "\n";
                $contents .= 'Route::post(\'sentry-tunnel\', SentryTunnelController::class);';

                $this->storage->targetDisk->put('routes/api.php', $contents . "\n");
            }
        }

        if (in_array('Custom Error Pages', $this->options)) {
            $this->storage->publish('templates/error-pages');
        }
    }

    protected function afterComposerInstallInertia(): void
    {
        if (!in_array('Inertia.js', $this->options)) {
            return;
        }

        $this->runProcess('php artisan inertia:middleware --ansi');

        $file = 'templates/app.' . (int) in_array('Sentry', $this->options) . '.js';
        $this->storage->publish($file, 'resources/js/app.js');
    }

    protected function initializeInstallerResources(): void
    {
        $this->composer = $this->findComposer();
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

    protected function determineAppData(): void
    {
        $this->line('The folder name is determined in accordance with Git based on the name of the app.');
        $this->line('If the folder already exists, it may not contain any files or folders created by Laravel.');

        $this->appName = text(
            label: 'Name of the new app',
            default: $this->appName,
            required: true,
            //hint: implode(' ', [
            //    'The folder name is determined in accordance with Git based on the name of the app.',
            //    'If the folder already exists, it may not contain any files or folders created by Laravel.',
            //])
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

    protected function initializeStorage(): void
    {
        $reflection = new ReflectionClass(get_called_class());

        $this->storage = new Storage(
            targetPath: rtrim(getcwd(), '/\\') . DIRECTORY_SEPARATOR . $this->appPath,
            packagePath: dirname($reflection->getFileName(), 3)
        );
    }
}
