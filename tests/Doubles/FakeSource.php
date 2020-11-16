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

use Shudd3r\PackageFiles\Token;
use Shudd3r\PackageFiles\Token\Reader\Source;


class FakeSource implements Source
{
    public int    $reads   = 0;
    public ?Token $created = null;

    private ?string $value;

    public function __construct(?string $value)
    {
        $this->value = $value;
    }

    public function create(string $value): ?Token
    {
        return $this->created = isset($this->value) ? new FakeToken($value) : null;
    }

    public function value(): string
    {
        $this->reads++;
        return $this->value ?? '';
    }
}
