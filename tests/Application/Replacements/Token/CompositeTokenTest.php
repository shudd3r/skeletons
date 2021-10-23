<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Replacements\Token;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Replacements\Token;


class CompositeTokenTest extends TestCase
{
    public function testTokenReplacesAllInternalPlaceholders()
    {
        $tokens = [
            new Token\ValueToken('foo.token', 'foo'),
            new Token\ValueToken('bar.token', 'bar'),
            new Token\ValueToken('baz.token', 'baz')
        ];

        $token = new Token\CompositeToken(...$tokens);
        $template = "Template with {foo.token}-{bar.token}-{baz.token}";

        $this->assertSame('Template with foo-bar-baz', $token->replace($template));
    }
}
