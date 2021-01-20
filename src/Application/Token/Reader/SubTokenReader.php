<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token\Reader;

use Shudd3r\PackageFiles\Application\Token;
use Shudd3r\PackageFiles\Application\Token\Reader;


class SubTokenReader implements Reader
{
    private ValueReader $reader;
    private string      $subtypeName;
    private             $transform;

    public function __construct(ValueReader $reader, string $subtypeName, callable $transform)
    {
        $this->reader      = $reader;
        $this->subtypeName = $subtypeName;
        $this->transform   = $transform;
    }

    public function token(string $namespace = ''): ?Token
    {
        $token = $this->reader->token($namespace);
        return $token ? new Token\CompositeToken($token, $this->subToken($namespace)) : null;
    }

    public function value(): string
    {
        return $this->reader->value();
    }

    private function subToken(string $namespace): Token
    {
        $mutated = ($this->transform)($this->reader->value());
        return new Token\ValueToken($namespace . '.' . $this->subtypeName, $mutated);
    }
}
