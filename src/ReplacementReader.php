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


class ReplacementReader
{
    private RuntimeEnv  $env;
    private Replacement $replacement;

    private ?ValueToken $initialToken;

    public function __construct(RuntimeEnv $env, Replacement $replacement)
    {
        $this->env         = $env;
        $this->replacement = $replacement;
    }

    final public function initialToken(string $name, array $options): ?ValueToken
    {
        if (isset($this->initialToken)) { return $this->initialToken; }

        $default = $this->commandLineOption($options) ?? $this->replacement->defaultValue($this->env, $options);
        $initial = $this->inputString($options, $this->replacement->isValid($default) ? $default : '');

        return $this->initialToken = $this->replacement->token($name, $initial);
    }

    final public function validationToken(string $name): ?ValueToken
    {
        return $this->replacement->token($name, $this->metaDataValue($name));
    }

    final public function updateToken(string $name, array $options): ?ValueToken
    {
        $value = $this->inputString($options, $this->commandLineOption($options) ?? $this->metaDataValue($name));
        return $this->replacement->token($name, $value);
    }

    private function commandLineOption(array $options): ?string
    {
        $option = $this->replacement->optionName();
        return $option ? $options[$option] ?? null : null;
    }

    private function inputString(array $options, string $default): string
    {
        $prompt = $this->replacement->inputPrompt();
        $inputEnabled = $prompt && (isset($options['i']) || isset($options['interactive']));
        if (!$inputEnabled) { return $default; }

        $promptPostfix = $default ? ' [default: `' . $default . '`]:' : ':';
        return $this->env->input()->value($prompt . $promptPostfix) ?: $default;
    }

    private function metaDataValue(string $namespace): string
    {
        return $this->env->metaData()->value($namespace) ?? '';
    }
}
