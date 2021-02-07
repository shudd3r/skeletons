<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Doubles;

use Shudd3r\PackageFiles\Application\Token\Reader;
use Shudd3r\PackageFiles\Application\Token;


class FakeReader extends Reader
{
    private ?string $value;
    private ?Token  $token;

    public function __construct(?string $value = 'foo')
    {
        $this->value = $value;
        $this->token = isset($value) ? new FakeToken($value) : null;
        parent::__construct(fn() => null, []);
    }

    public function token(string $namespace = ''): ?Token
    {
        return isset($this->value) ? new FakeToken($this->value, $namespace) : null;
    }

    public function value(): string
    {
        return isset($this->value) ? $this->value : 'invalid string';
    }
}
