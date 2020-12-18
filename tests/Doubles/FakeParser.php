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

use Shudd3r\PackageFiles\Token\Parser;


class FakeParser implements Parser
{
    private string $value;
    private bool   $isValid;

    public function __construct(string $value = 'foo', bool $validationResult = true)
    {
        $this->value   = $value;
        $this->isValid = $validationResult;
    }

    public function isValid(string $value): bool
    {
        return $this->isValid;
    }

    public function parsedValue(): string
    {
        return $this->value;
    }
}
