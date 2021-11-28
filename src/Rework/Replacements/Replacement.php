<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Rework\Replacements;

use Shudd3r\Skeletons\Replacements\Token;


abstract class Replacement
{
    protected ?string $inputPrompt  = null;
    protected ?string $argumentName = null;
    protected string  $description  = '';

    final public function token(string $name, Source $source): ?Token
    {
        $value = $this->value($name, $source);
        return $this->isValid($value) ? $this->tokenInstance($name, $value) : null;
    }

    final public function description(string $name): string
    {
        if (!$this->argumentName) { return ''; }

        $description = $this->description ?: 'Unspecified replacement for {%s} placeholder';
        $lines       = explode("\n", str_replace('%s', $name, $description));
        $argInfo     = '  ' . str_pad($this->argumentName, 11) . ' ' . array_shift($lines);

        foreach ($lines as $line) {
            $argInfo .= PHP_EOL . '              ' . $line;
        }

        return $argInfo;
    }

    protected function tokenInstance($name, $value): Token
    {
        return new Token\BasicToken($name, $value);
    }

    abstract protected function isValid(string $value): bool;

    abstract protected function resolvedValue(Source $source): string;

    private function value(string $name, Source $source): string
    {
        $default = $this->defaultValue($name, $source);
        if (!$this->inputPrompt) { return $default; }
        if (!$this->isValid($default)) { $default = ''; }

        $prompt  = '  > ' . $this->inputPrompt . ($default ? ' [default: ' . $default . ']' : '') . ':';
        $isValid = fn (string $value) => $this->isValid($value);

        return $source->inputString($prompt, $isValid) ?: $default;
    }

    private function defaultValue(string $name, Source $source): string
    {
        if ($this->argumentName) {
            $value = $source->commandArgument($this->argumentName);
            if ($this->isValid($value)) { return $value; }
        }

        return $source->metaValueOf($name) ?? $this->resolvedValue($source);
    }
}
