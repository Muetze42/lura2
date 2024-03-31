<?php

namespace NormanHuth\Luraa\Commands;

use Illuminate\Support\Str;
use NormanHuth\Luraa\Services\EnvFile;
use NormanHuth\Luraa\Support\Process;
use NormanHuth\Luraa\Support\Storage;

use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\text;
use function NormanHuth\Luraa\ci;

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

    protected EnvFile $env;

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
        $this->determineAppData();
        if (!$this->isTargetPathOk()) {
            return;
        }

        $this->options = multiselect(
            label: 'Select optional packages to install',
            options: array_keys($this->options),
            default: array_keys(array_filter($this->options)),
            scroll: count($this->options),
        );

        $this->env = new EnvFile($this->storage->targetDisk()->path('.env.example'));

        $this->moveTempContentBack();
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

        $this->composer = $this->findComposer();
        $result = Process::path($this->storage->cwd())->run(ci($command));

        $this->line($result->output());
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
                    'The target path „%s“ already contains Laravel files or directories.',
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

        $this->storage = new Storage(rtrim(getcwd(), '/\\') . DIRECTORY_SEPARATOR . $this->appPath);

        if ($validated || empty($this->appPath)) {
            $this->determineAppData();
        }
    }
}
