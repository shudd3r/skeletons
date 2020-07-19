<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Token;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Token\Description;
use Exception;


class DescriptionTest extends TestCase
{
    public function testTokenReplacesInternalPlaceholder()
    {
        $token    = new Description('Some text');
        $template = 'Template with ' . Description::TEXT;

        $this->assertSame('Template with Some text', $token->replacePlaceholders($template));
    }

    public function testEmptyText_ThrowsException()
    {
        $this->expectException(Exception::class);
        new Description('');
    }
}
