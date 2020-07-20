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
use Shudd3r\PackageFiles\Token\MainNamespace;
use Exception;


class MainNamespaceTest extends TestCase
{
    public function testTokenReplacesInternalPlaceholder()
    {
        $token    = new MainNamespace('Foo\\Bar');
        $template = 'Template with ' . MainNamespace::SRC . ' and escaped ' . MainNamespace::SRC_ESC;

        $this->assertSame('Template with Foo\\Bar and escaped Foo\\\\Bar', $token->replacePlaceholders($template));
    }

    /**
     * @dataProvider exampleNamespaces
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testInvalidNamespace_ThrowsException(string $invalid, string $valid)
    {
        new MainNamespace($valid);
        $this->expectException(Exception::class);
        new MainNamespace($invalid);
    }

    public function exampleNamespaces()
    {
        return [
            ['Foo/Bar', 'Foo\Bar'],
            ['_Foo\1Bar\Baz', '_Foo\_1Bar\Baz'],
            ['Package:000\na_Me', 'Package000\na_Me']];
    }
}
