<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Token\Reader;

use Shudd3r\PackageFiles\Token\Reader;
use Shudd3r\PackageFiles\Application\Output;
use Shudd3r\PackageFiles\Token;
use Exception;


class TokenGroupReader implements Reader
{
    /** @var callable[] fn() => Token */
    private array  $factories;
    private Output $output;

    public function __construct(Output $output, callable ...$factories)
    {
        $this->output    = $output;
        $this->factories = $factories;
    }

    public function token(): Token
    {
        $tokens     = [];
        $unresolved = false;
        foreach ($this->factories as $factory) {
            $token = $this->createToken($factory);
            if (!$token) { $unresolved = true; }
            $tokens[] = $token;
        }

        if ($unresolved) {
            throw new Exception('Cannot process unresolved tokens');
        }

        return new Token\TokenGroup(...$tokens);
    }

    private function createToken(callable $callback): ?Token
    {
        try {
            return $callback();
        } catch (Exception $e) {
            $this->output->send($e->getMessage(), 1);
            return null;
        }
    }
}
