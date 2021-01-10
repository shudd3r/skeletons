<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token\ReaderFactory;

use Shudd3r\PackageFiles\Application\Token\ReaderFactory;
use Shudd3r\PackageFiles\Application\Token\Reader;
use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Application\RuntimeEnv;


abstract class ValueReaderFactory implements ReaderFactory
{
    protected RuntimeEnv $env;
    protected array      $options;

    protected ?string $inputPrompt;
    protected ?string $optionName;

    private Reader $initializeReader;
    private Reader $validationReader;
    private Reader $updateReader;

    public function __construct(RuntimeEnv $env, array $options)
    {
        $this->env     = $env;
        $this->options = $options;
    }

    public function initializationReader(): Reader
    {
        return $this->initializeReader ??= $this->newReaderInstance($this->defaultSource());
    }

    public function validationReader(Source $metaDataSource): Reader
    {
        return $this->validationReader ??= $this->newReaderInstance($metaDataSource);
    }

    public function updateReader(Source $metaDataSource): Reader
    {
        return $this->updateReader ??= $this->newReaderInstance($this->userSource($metaDataSource));
    }

    abstract protected function defaultSource(): Source;

    abstract protected function newReaderInstance(Source $source): Reader;

    protected function userSource(Source $source): Source
    {
        if (isset($this->optionName)) {
            $source = $this->option($this->optionName, $source);
        }

        if (isset($this->inputPrompt)) {
            $source = $this->interactive($this->inputPrompt, $source);
        }

        return $source;
    }

    private function option(string $name, Source $default): Source
    {
        return isset($this->options[$name])
            ? new Source\PredefinedValue($this->options[$name])
            : $default;
    }

    private function interactive(string $prompt, Source $source): Source
    {
        return isset($this->options['i']) || isset($this->options['interactive'])
            ? new Source\InteractiveInput($prompt, $this->env->input(), $source)
            : $source;
    }
}
