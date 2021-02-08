<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token;

use Shudd3r\PackageFiles\Application\RuntimeEnv;


abstract class Replacement
{
    protected RuntimeEnv $env;
    protected array      $options;

    protected ?string $inputPrompt;
    protected ?string $optionName;

    private ?ValueToken $initialToken;

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
        $value = $this->metaDataSource($name)->value();
        return $this->token($name, $value);
    }

    public function updateToken(string $name): ?ValueToken
    {
        $value = $this->userSource($this->metaDataSource($name))->value();
        return $this->token($name, $value);
    }

    protected function token(string $name, string $value): ?ValueToken
    {
        return $this->isValid($value) ? new ValueToken($name, $value) : null;
    }

    abstract protected function isValid(string $value): bool;

    abstract protected function defaultSource(): Source;

    protected function metaDataSource(string $namespace): Source
    {
        $callback = fn() => $this->env->metaData()->value($namespace) ?? '';
        return new Source\CallbackSource($callback);
    }

    protected function userSource(Source $source): Source
    {
        return $this->interactive($this->option($source));
    }

    private function option(Source $default): Source
    {
        $hasOption = isset($this->optionName) && isset($this->options[$this->optionName]);
        return $hasOption
            ? new Source\PredefinedValue($this->options[$this->optionName])
            : $default;
    }

    private function interactive(Source $source): Source
    {
        $fromInput = isset($this->inputPrompt) && (isset($this->options['i']) || isset($this->options['interactive']));
        return $fromInput
            ? new Source\InteractiveInput($this->inputPrompt, $this->env->input(), $source)
            : $source;
    }
}
