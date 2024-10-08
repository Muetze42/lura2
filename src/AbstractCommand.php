<?php

namespace NormanHuth\Lura;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory as Validator;
use NormanHuth\Library\ClassFinder;
use NormanHuth\Lura\Contracts\FeatureInterface;
use NormanHuth\Lura\Support\Process;
use NormanHuth\Lura\Support\Storage;
use ReflectionClass;

abstract class AbstractCommand extends Command
{
    /**
     * The Validator instance.
     */
    protected ?Validator $validator = null;

    protected array $validatorMessages;

    protected string $appPath;

    public Storage $storage;

    /**
     * Create a new console command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create a new Validator instance.
     */
    protected function registerValidator(): void
    {
        $loader = new FileLoader(new Filesystem(), '');
        $translator = new Translator($loader, 'en');
        $this->validator = new Validator($translator, new Container());
        $this->validatorMessages = include __DIR__ . '/../lang/en/validation.php';
    }

    /**
     * Validate the given data against the provided rules.
     */
    protected function validate(array $data, array $rules): bool
    {
        if (! $this->validator) {
            $this->registerValidator();
        }

        $validator = $this->validator->make($data, $rules, $this->validatorMessages);
        if ($validator->fails()) {
            foreach ($validator->messages()->toArray() as $errors) {
                foreach ($errors as $error) {
                    $this->error($error);
                }
            }
        }

        return $validator->fails();
    }

    /**
     * Get the composer command for the environment.
     */
    protected function findComposer(): string
    {
        $composerPath = getcwd() . '/composer.phar';

        return file_exists($composerPath) ? '"' . PHP_BINARY . '" ' . $composerPath : 'composer';
    }

    public function getMigrationPrefixedFileName(string $name): string
    {
        return date('Y_m_d_') . '000000_' . Str::snake(trim($name, '_')) . '.php';
    }

    public function runProcess(string|array $command, ?string $path = null): void
    {
        if (! $path) {
            $path = $this->storage->targetPath();
        }
        if (is_array($command)) {
            $command = implode(' ', $command);
        }

        Process::path($path)
            ->run($command, function (string $type, string $output) {
                $this->output->write($output);
            });
    }

    protected function initializeStorage(): void
    {
        $reflection = new ReflectionClass(get_called_class());

        $this->storage = new Storage(
            targetPath: rtrim(getcwd(), '/\\') . DIRECTORY_SEPARATOR . $this->appPath,
            packagePath: dirname($reflection->getFileName(), 3)
        );
    }

    protected function getFeatures(string $type = 'Laravel'): array
    {
        return Arr::where(ClassFinder::load(
            paths: __DIR__ . '/Features/' . $type,
            subClassOf: FeatureInterface::class
        ), fn (FeatureInterface|string $feature) => $feature::autoload());
    }
}
