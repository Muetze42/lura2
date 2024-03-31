<?php

namespace NormanHuth\Luraa\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory as Validator;

abstract class AbstractCommand extends Command
{
    /**
     * The Validator instance.
     */
    protected ?Validator $validator = null;

    protected array $validatorMessages;

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
    public function findComposer(): string
    {
        $composerPath = getcwd() . '/composer.phar';

        return file_exists($composerPath) ? '"' . PHP_BINARY . '" ' . $composerPath : 'composer';
    }
}
