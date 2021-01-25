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
use Shudd3r\PackageFiles\Application\Token\ValueToken;


class ValueTokenTest extends TestCase
{
    public function testPlaceholderIsReplaced()
    {
        $token = new ValueToken('replace', 'bar');
        $this->assertSame('foo bar', $token->replacePlaceholders('foo {replace}'));
    }

    public function testMetaData_ReturnsCorrectKeyValuePair()
    {
        $token = new ValueToken('foo', 'bar');
        $this->assertSame(['foo' => 'bar'], $token->metaData());
    }
}
