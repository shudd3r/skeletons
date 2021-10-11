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
    private array $options;

    /** @var ReplacementReader[] */
    private array $replacements;
    private array $currentRefs;

    public function __construct(array $options, array $replacements = [])
    {
        $this->options      = $options;
        $this->replacements = $replacements;
    }

    public function valueOf(string $replacementName): string
    {
        $isCurrentRef = $this->currentRefs[$replacementName] ?? false;
        if ($isCurrentRef) { return ''; }

        $replacement = $this->replacements[$replacementName] ?? null;
        if (!$replacement) { return ''; }

        $this->currentRefs[$replacementName] = true;
        $token = $replacement->initialToken($replacementName, $this->options, $this);
        $this->currentRefs[$replacementName] = false;

        return $token ? $token->value() : '';
    }

    public function initialTokens(): array
    {
        $readMethod = fn(string $name, ReplacementReader $replacement) => $replacement->initialToken($name, $this->options, $this);
        return $this->readTokens($readMethod);
    }

    public function validationTokens(): array
    {
        $readMethod = fn(string $name, ReplacementReader $replacement) => $replacement->validationToken($name);
        return $this->readTokens($readMethod);
    }

    public function updateTokens(): array
    {
        $readMethod = fn(string $name, ReplacementReader $replacement) => $replacement->updateToken($name, $this->options);
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
