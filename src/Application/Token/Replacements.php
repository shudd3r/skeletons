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

use Shudd3r\PackageFiles\ReplacementReader;


class Replacements
{
    /** @var ReplacementReader[] */
    private array $replacements;
    private array $currentRefs;

    public function __construct(array $replacements = [])
    {
        $this->replacements = $replacements;
    }

    public function valueOf(string $replacementName, array $options): string
    {
        $isCurrentRef = $this->currentRefs[$replacementName] ?? false;
        if ($isCurrentRef) { return ''; }

        $replacement = $this->replacements[$replacementName] ?? null;
        if (!$replacement) { return ''; }

        $this->currentRefs[$replacementName] = true;
        $token = $replacement->initialToken($replacementName, $options, $this);
        $this->currentRefs[$replacementName] = false;

        return $token ? $token->value() : '';
    }

    public function initialTokens(array $options): array
    {
        $readMethod = fn(string $name, ReplacementReader $replacement) => $replacement->initialToken($name, $options, $this);
        return $this->readTokens($readMethod);
    }

    public function validationTokens(): array
    {
        $readMethod = fn(string $name, ReplacementReader $replacement) => $replacement->validationToken($name);
        return $this->readTokens($readMethod);
    }

    public function updateTokens(array $options): array
    {
        $readMethod = fn(string $name, ReplacementReader $replacement) => $replacement->updateToken($name, $options);
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
