<?php

namespace NormanHuth\Luraa\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory as Validator;
use NormanHuth\Library\Lib\MacroRegistry;

abstract class AbstractCommand extends Command
{
    /**
     * The Validator instance.
     */
    protected ?Validator $validator = null;

    protected array $validatorMessages;

    protected bool $promptsUnsupportedEnvironment;

    /**
     * Create a new console command instance.
     */
    public function __construct()
    {
        parent::__construct();
        MacroRegistry::registerStrMacros();
        $this->promptsUnsupportedEnvironment = windows_os();
    }

    /**
     * Create a new Validator instance.
     */
    protected function registerValidator(): void
    {
        $loader = new FileLoader(new Filesystem(), '');
        $translator = new Translator($loader, 'en');
        $this->validator = new Validator($translator, new Container());
        $this->validatorMessages = include __DIR__ . '/../../lang/en/validation.php';
    }

    /**
     * Validate the given data against the provided rules.
     */
    protected function validate(array $data, array $rules): bool
    {
        if (!$this->validator) {
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

    protected function getMigrationPrefixedFileName(string $name): string
    {
        return date('Y_m_d_') . '000000_' . Str::snake(trim($name, '_')) . '.php';
    }
}
