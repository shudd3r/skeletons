<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Token;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Token;


class TokenCacheTest extends TestCase
{
    public function testGettingTokenFromCache()
    {
        $predefinedToken = new Token\ValueToken('foo', 'one');
        $addedToken      = new Token\ValueToken('bar', 'two');

        $tokens = new Token\TokenCache(['name' => $predefinedToken]);
        $tokens->add('foo/bar.php', $addedToken);

        $this->assertSame($predefinedToken, $tokens->token('name'));
        $this->assertSame($addedToken, $tokens->token('foo/bar.php'));
    }

    public function testMissingToken_ReturnsNull()
    {
        $tokens = new Token\TokenCache();
        $this->assertNull($tokens->token('someName'));
    }
}
