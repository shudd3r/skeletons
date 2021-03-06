<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Replacements\Token;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Replacements\Token\BasicToken;


class BasicTokenTest extends TestCase
{
    public function testPlaceholderIsReplaced()
    {
        $token = new BasicToken('replace', 'bar');
        $this->assertSame('foo bar', $token->replace('foo {replace}'));
    }

    public function testValueMethod_ReturnsCorrectValue()
    {
        $token = new BasicToken('foo', 'bar');
        $this->assertSame('bar', $token->value());
    }
}
