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
use Shudd3r\PackageFiles\Application\Token\Replacements;
use Shudd3r\PackageFiles\Application\Token\ValueToken;


class ReplacementReader
{
    private Replacement $replacement;
    private RuntimeEnv  $env;
    private array       $options;

    private ?ValueToken $initialToken;

    public function __construct(Replacement $replacement, RuntimeEnv $env, array $options)
    {
        $this->replacement = $replacement;
        $this->env         = $env;
        $this->options     = $options;
    }

    final public function initialToken(string $name, Replacements $replacements): ?ValueToken
    {
        if (isset($this->initialToken)) { return $this->initialToken; }

        $default = $this->commandLineOption() ?? $this->replacement->defaultValue($this->env, $replacements);
        $initial = $this->inputString($this->replacement->isValid($default) ? $default : '');

        return $this->initialToken = $this->replacement->token($name, $initial);
    }

    final public function validationToken(string $name): ?ValueToken
    {
        return $this->replacement->token($name, $this->metaDataValue($name));
    }

    final public function updateToken(string $name): ?ValueToken
    {
        $value = $this->inputString($this->commandLineOption() ?? $this->metaDataValue($name));
        return $this->replacement->token($name, $value);
    }

    private function commandLineOption(): ?string
    {
        $option = $this->replacement->optionName();
        return $option ? $this->options[$option] ?? null : null;
    }

    private function inputString(string $default): string
    {
        $prompt = $this->replacement->inputPrompt();
        $inputEnabled = $prompt && (isset($this->options['i']) || isset($this->options['interactive']));
        if (!$inputEnabled) { return $default; }

        $promptPostfix = $default ? ' [default: `' . $default . '`]:' : ':';
        return $this->env->input()->value($prompt . $promptPostfix) ?: $default;
    }

    private function metaDataValue(string $namespace): string
    {
        return $this->env->metaData()->value($namespace) ?? '';
    }
}
