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

use Shudd3r\PackageFiles\Application\Token;


class FakeToken implements Token
{
    private string $value;

    public function __construct(string $value = 'foo')
    {
        $this->value = $value;
    }

    public function replacePlaceholders(string $template): string
    {
        return $template;
    }
}
