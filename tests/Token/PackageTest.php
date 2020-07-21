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
use Shudd3r\PackageFiles\Token\Package;
use Exception;


class PackageTest extends TestCase
{
    public function testTokenReplacesInternalPlaceholder()
    {
        $token    = new Package('foo/bar');
        $template = 'Template with ' . Package::NAME . ' and title ' . Package::TITLE;

        $this->assertSame('Template with foo/bar and title Foo/Bar', $token->replacePlaceholders($template));
    }

    /**
     * @dataProvider examplePackages
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testInvalidNamespace_ThrowsException(string $invalid, string $valid)
    {
        new Package($valid);
        $this->expectException(Exception::class);
        new Package($invalid);
    }

    public function examplePackages()
    {
        return [
            ['-Packa-ge1/na.me', 'Packa-ge1/na.me'],
            ['1Package000_/na_Me', '1Package000/na_Me']
        ];
    }
}
