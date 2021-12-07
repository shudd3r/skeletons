<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Replacements\Replacement;

use Shudd3r\Skeletons\Replacements\Replacement;
use Shudd3r\Skeletons\Replacements\Source;
use Shudd3r\Skeletons\Replacements\Token;


abstract class StandardReplacement implements Replacement
{
    protected ?string $inputPrompt  = null;
    protected ?string $argumentName = null;
    protected string  $description  = '';
    protected int     $inputTries   = 3;

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
        $argValue = $this->argumentName ? $source->commandArgument($this->argumentName) : null;
        $validArg = $argValue !== null && $this->isValid($argValue);
        $default  = $validArg ? $argValue : $source->metaValueOf($name) ?? $this->resolvedValue($source);

        if (!$this->inputPrompt) { return $argValue ?? $default; }
        if (!$this->isValid($default)) { $default = ''; }

        $prompt = $this->inputPrompt . ($default ? ' [default: ' . $default . ']' : '');
        $input  = $source->inputValue($prompt);
        if ($input === null) { return $argValue ?? $default; }

        $tries   = $this->inputTries;
        $isValid = fn (string $value) => (!$value && $default) || $this->isValid($value);
        while (!$isValid($input) && --$tries) {
            $retryInfo = $tries === 1 ? 'once more' : 'again';
            $source->sendMessage('Invalid value. Try ' . $retryInfo);
            $input = $source->inputValue($prompt);
        }

        if (!$tries) {
            $source->sendMessage('Invalid value. Try `help` command for information on this value format.');
        }

        return $input ?: $default;
    }
}
