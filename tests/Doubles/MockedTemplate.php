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

use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Application\Token;


class MockedTemplate implements Template
{
    private ?Token $receivedToken = null;
    private string $rendered;

    public function __construct(string $rendered)
    {
        $this->rendered = $rendered;
    }

    public function render(Token $token): string
    {
        $this->receivedToken = $token;
        return $this->rendered;
    }

    public function receivedToken(): ?Token
    {
        return $this->receivedToken;
    }
}
