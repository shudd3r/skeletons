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

use Shudd3r\PackageFiles\Application\Token;


class Reader
{
    private $createMethod;
    private array $readerFactories;

    private array $tokens;

    /**
     * @param callable $createMethod    fn(string, ReaderFactory) => Reader
     * @param array    $readerFactories
     */
    public function __construct(callable $createMethod, array $readerFactories)
    {
        $this->createMethod    = $createMethod;
        $this->readerFactories = $readerFactories;
    }

    public function token(): ?Token
    {
        $this->tokens ??= $this->readTokens();
        return $this->validTokens() ? new Token\CompositeToken(...array_values($this->tokens)) : null;
    }

    public function value(): string
    {
        isset($this->tokens) or $this->token();

        $values = [];
        foreach ($this->tokens as $name => $token) {
            $values[$name] = $token->value();
        }

        return json_encode($values, JSON_PRETTY_PRINT);
    }

    private function readTokens(): array
    {
        $tokens = [];
        foreach ($this->readerFactories as $name => $replacement) {
            $token = ($this->createMethod)($name, $replacement);
            if (!$token) { continue; }
            $tokens[$name] = $token;
        }

        return $tokens;
    }

    private function validTokens(): bool
    {
        return count($this->tokens) === count($this->readerFactories);
    }
}
