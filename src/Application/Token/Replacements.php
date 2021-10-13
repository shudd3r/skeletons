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

use Shudd3r\PackageFiles\Application\Token\Reader\FallbackReader;
use Shudd3r\PackageFiles\ReplacementReader;


class Replacements implements FallbackReader
{
    /** @var ReplacementReader[] */
    private array $replacements;
    private array $currentRefs;

    public function __construct(array $replacements = [])
    {
        $this->replacements = $replacements;
    }

    public function valueOf(string $name): string
    {
        $isCurrentRef = $this->currentRefs[$name] ?? false;
        if ($isCurrentRef) { return ''; }

        $replacement = $this->replacements[$name] ?? null;
        if (!$replacement) { return ''; }

        $this->currentRefs[$name] = true;
        $token = $replacement->initialToken($name, $this);
        $this->currentRefs[$name] = false;

        return $token ? $token->value() : '';
    }

    public function initialTokens(): array
    {
        $readMethod = fn(string $name, ReplacementReader $replacement) => $replacement->initialToken($name, $this);
        return $this->readTokens($readMethod);
    }

    public function validationTokens(): array
    {
        $readMethod = fn(string $name, ReplacementReader $replacement) => $replacement->validationToken($name);
        return $this->readTokens($readMethod);
    }

    public function updateTokens(): array
    {
        $readMethod = fn(string $name, ReplacementReader $replacement) => $replacement->updateToken($name);
        return $this->readTokens($readMethod);
    }

    private function readTokens(callable $readMethod): array
    {
        $tokens = [];
        foreach ($this->replacements as $name => $replacement) {
            $tokens[$name] = $readMethod($name, $replacement);
        }

        return $tokens;
    }
}
