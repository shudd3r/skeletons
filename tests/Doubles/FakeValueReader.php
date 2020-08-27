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

use Shudd3r\PackageFiles\Token\Reader\ValueReader;
use Shudd3r\PackageFiles\Token;


class FakeValueReader extends ValueReader
{
    protected const PROMPT = 'Prompt';
    protected const OPTION = 'option';

    public int   $reads = 0;
    public Token $created;

    private string $value;

    public function __construct(string $value = '')
    {
        $this->value   = $value;
        $this->created = new FakeToken($value);
    }

    public function createToken(string $value): Token
    {
        return $this->created = new FakeToken($value);
    }

    public function value(): string
    {
        $this->reads++;
        return $this->value;
    }
}
