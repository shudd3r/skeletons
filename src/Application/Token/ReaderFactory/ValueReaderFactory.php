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
use Shudd3r\PackageFiles\Application\Token\ValueToken;
use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Application\RuntimeEnv;


abstract class ValueReaderFactory implements ReaderFactory
{
    protected RuntimeEnv $env;
    protected array      $options;

    protected ?string $inputPrompt;
    protected ?string $optionName;

    private ValueToken $initialToken;

    public function __construct(RuntimeEnv $env, array $options)
    {
        $this->env     = $env;
        $this->options = $options;
    }

    public function initialToken(string $name): ?ValueToken
    {
        return $this->initialToken ??= $this->token($name, $this->defaultSource()->value());
    }

    public function validationToken(string $name): ?ValueToken
    {
        return $this->token($name, $this->metaDataSource($name)->value());
    }

    public function updateToken(string $name): ?ValueToken
    {
        return $this->token($name, $this->userSource($this->metaDataSource($name))->value());
    }

    abstract public function token(string $name, string $value): ?ValueToken;

    abstract protected function defaultSource(): Source;

    protected function metaDataSource(string $namespace): Source
    {
        return new Source\CallbackSource(fn() => $this->env->metaData()->value($namespace) ?? '');
    }

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
