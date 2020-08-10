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
use Shudd3r\PackageFiles\Token\Repository;
use Exception;


class RepositoryTest extends TestCase
{
    public function testTokenReplacesInternalPlaceholder()
    {
        $token    = new Repository('foo/bar');
        $template = 'Template with ' . Repository::NAME;

        $this->assertSame('Template with foo/bar', $token->replacePlaceholders($template));
    }

    /**
     * @dataProvider exampleRepositories
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testInvalidNamespace_ThrowsException(string $invalid, string $valid)
    {
        new Repository($valid);
        $this->expectException(Exception::class);
        new Repository($invalid);
    }

    public function exampleRepositories()
    {
        $name = function (int $length) { return str_pad('x', $length, 'x'); };

        $longAccount  = $name(40) . '/name';
        $shortAccount = $name(39) . '/name';
        $longRepo     = 'user/' . $name(101);
        $shortRepo    = 'user/' . $name(100);

        return [
            ['repo/na(me)', 'repo/na-me'],
            ['-repo/name', 'r-epo/name'],
            ['repo_/name', 'repo/name'],
            ['re--po/name', 're-po/name'],
            [$longAccount, $shortAccount],
            [$longRepo, $shortRepo]
        ];
    }
}
