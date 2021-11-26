<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Replacements;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Replacements\TokenCache;
use Shudd3r\Skeletons\Replacements\Token\BasicToken;


class TokenCacheTest extends TestCase
{
    public function testGettingTokenFromCache()
    {
        $initialToken = new BasicToken('foo', 'one');
        $addedToken   = new BasicToken('bar', 'two');

        $tokens = new TokenCache(['name' => $initialToken]);
        $tokens->add('foo/bar.php', $addedToken);

        $this->assertSame($initialToken, $tokens->token('name'));
        $this->assertSame($addedToken, $tokens->token('foo/bar.php'));
    }

    public function testMissingToken_ReturnsNull()
    {
        $tokens = new TokenCache();
        $this->assertNull($tokens->token('someName'));
    }
}
