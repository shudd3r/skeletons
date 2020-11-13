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

use Shudd3r\PackageFiles\Token\Reader;
use Shudd3r\PackageFiles\Token;


class FakeReader implements Reader
{
    private ?string $value;
    private string  $placeholder;

    public function __construct(?string $value = 'foo/bar', string $placeholder = '{fake.placeholder}')
    {
        $this->value       = $value;
        $this->placeholder = $placeholder;
    }

    public function token(): ?Token
    {
        return isset($this->value) ? FakeToken::withPlaceholder($this->placeholder, $this->value) : null;
    }
}
