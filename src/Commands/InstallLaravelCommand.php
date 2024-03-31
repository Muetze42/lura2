<?php

namespace NormanHuth\Luraa\Commands;

use Illuminate\Support\Str;
use NormanHuth\Luraa\Contracts\ComposerInstallTrait;
use NormanHuth\Luraa\Contracts\CreateProjectTrait;
use NormanHuth\Luraa\Services\DependenciesFilesService;
use NormanHuth\Luraa\Services\EnvFileService;
use NormanHuth\Luraa\Support\Process;
use NormanHuth\Luraa\Support\Storage;
use ReflectionClass;

use function Laravel\Prompts\intro;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;

class InstallLaravelCommand extends AbstractCommand
{
    use ComposerInstallTrait;
    use CreateProjectTrait;

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
    ];

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

        $this->determineOptions();

        spin(
            fn () => $this->initializeInstallerResources(),
            'Initializing installer resources'
        );

        $this->createProject();

        $this->composerInstall();

        spin(
            fn () => $this->loadInstallerResources(),
            'Loading additional installer resources'
        );

        $this->moveTempContentBack();

        outro(
            sprintf(
                'Your project "%s" has been successfully created at "%s"',
                $this->appName,
                $this->storage->targetPath()
            )
        );
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

        Process::path($this->storage->cwdPath())
            ->run(ci($command), function (string $type, string $output) {
                $this->line($output);
            });
    }

    protected function executeComposerInstall(): void
    {
        $command = [
            $this->composer,
            'install',
            '--prefer-dist',
            '--ansi',
        ];

        Process::path($this->storage->targetPath())
            ->run(ci($command), function (string $type, string $output) {
                $this->line($output);
            });
    }

    protected function initializeInstallerResources(): void
    {
        $this->composer = $this->findComposer();
    }

    protected function loadInstallerResources(): void
    {
        $this->env = new EnvFileService($this->storage->targetDisk()->path('.env.example'));
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
            label: 'Select optional packages to install',
            options: array_keys($this->options),
            default: array_keys(array_filter($this->options)),
            scroll: count($this->options),
        );
    }

    protected function beforeCreateProject(): void
    {
        //
    }

    protected function afterCreateProject(): void
    {
        //
    }

    protected function moveTempContentBack(): void
    {
        if (!$this->tempPath) {
            return;
        }

        foreach ($this->storage->cwdDisk()->directories($this->tempPath) as $directory) {
            $this->storage->filesystem->moveDirectory(
                $this->storage->cwdDisk()->path($directory),
                $this->storage->targetDisk()->path(basename($directory)),
                true
            );
        }
        foreach ($this->storage->cwdDisk()->files($this->tempPath) as $file) {
            $this->storage->filesystem->move(
                $this->storage->cwdDisk()->path($file),
                $this->storage->targetDisk()->path(basename($file))
            );
        }
        $this->storage->cwdDisk()->deleteDirectory($this->tempPath);
    }

    protected function isTargetPathOk(): bool
    {
        $files = $this->storage->targetDisk()->files();
        $directories = $this->storage->targetDisk()->directories();

        if (!count($directories) && !count($files)) {
            return true;
        }

        $laravel = $this->storage->packageDisk()->json('data/laravel-file-structure.json');

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
            $this->storage->targetDisk()->path(''),
            $this->storage->cwdDisk()->path($this->tempPath)
        );

        return true;
    }

    protected function determineTempPath(): void
    {
        $temp = 'temp-' . Str::random();
        if ($this->storage->cwdDisk()->exists($temp)) {
            $this->determineTempPath();
        }
        $this->tempPath = $temp;
    }

    protected function determineAppData(): void
    {
        $this->line('The folder name is determined in accordance with Git based on the name of the app.');
        $this->line('If the folder already exists, it may not contain any files or folders created by Laravel.');

        $this->appName = text(
            label: 'What name should the app have?',
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
