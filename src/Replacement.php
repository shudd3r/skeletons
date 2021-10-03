<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles;

use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Application\Token\ValueToken;


abstract class Replacement
{
    private ?string $fallback;

    protected RuntimeEnv $env;

    protected ?string $inputPrompt;
    protected ?string $optionName;

    private ?ValueToken $initialToken;

    public function __construct(RuntimeEnv $env, ?string $fallbackPlaceholder = null)
    {
        $this->env      = $env;
        $this->fallback = $fallbackPlaceholder;
    }

    final public function initialToken(string $name, array $options): ?ValueToken
    {
        return $this->initialToken ??= $this->token($name, $this->initialValue($options));
    }

    final public function validationToken(string $name): ?ValueToken
    {
        return $this->token($name, $this->metaDataValue($name));
    }

    final public function updateToken(string $name, array $options): ?ValueToken
    {
        $value = $this->inputString($options, $this->commandLineOption($options) ?? $this->metaDataValue($name));
        return $this->token($name, $value);
    }

    final protected function initialValue(array $options): string
    {
        return $this->inputString($options, $this->commandLineOption($options) ?? $this->defaultValue($options));
    }

    protected function token(string $name, string $value): ?ValueToken
    {
        return $this->isValid($value) ? new ValueToken($name, $value) : null;
    }

    abstract protected function isValid(string $value): bool;

    abstract protected function defaultValue(array $options): string;

    protected function commandLineOption(array $options): ?string
    {
        return isset($this->optionName) ? $options[$this->optionName] ?? null : null;
    }

    protected function inputString(array $options, string $default): string
    {
        $inputEnabled = isset($this->inputPrompt) && (isset($options['i']) || isset($options['interactive']));
        if (!$inputEnabled) { return $default; }

        $promptPostfix = $default ? ' [default: `' . $default . '`]:' : ':';
        return $this->env->input()->value($this->inputPrompt . $promptPostfix) ?: $default;
    }

    protected function metaDataValue(string $namespace): string
    {
        return $this->env->metaData()->value($namespace) ?? '';
    }

    protected function fallbackValue(array $options): string
    {
        return $this->fallback ? $this->env->replacements()->valueOf($this->fallback, $options) : '';
    }
}
