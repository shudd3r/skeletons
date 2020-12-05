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

use Shudd3r\PackageFiles\TokenV2\Reader\ValueToken;
use Shudd3r\PackageFiles\Token;


class MockedValueToken extends ValueToken
{
    public FakeSourceV2 $source;

    private bool $valid;

    public function __construct(?string $sourceValue = 'foo', bool $valid = true)
    {
        $this->source = new FakeSourceV2($sourceValue);
        $this->valid  = $valid;
        parent::__construct($this->source);
    }

    public function isValid(string $value): bool
    {
        return $this->valid;
    }

    public function parsedValue(): string
    {
        return 'parsed value';
    }

    protected function newTokenInstance(string $value): Token
    {
        return new FakeToken($value);
    }
}
