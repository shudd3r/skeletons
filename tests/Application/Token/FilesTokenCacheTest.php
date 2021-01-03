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
use Shudd3r\PackageFiles\Application\Token\FilesTokenCache;
use Shudd3r\PackageFiles\Tests\Doubles;


class FilesTokenCacheTest extends TestCase
{
    public function testGettingTokenFromCache()
    {
        $predefinedToken = new Doubles\FakeToken();
        $addedToken      = new Doubles\FakeToken();

        $tokens = new FilesTokenCache(['some/file.ext' => $predefinedToken]);

        $file = $this->file('some/file.ext');
        $this->assertSame($predefinedToken, $tokens->token($file));

        $file = $this->file('foo/bar.php');
        $tokens->add($file, $addedToken);
        $this->assertSame($addedToken, $tokens->token($file));
    }

    private function file(string $filename)
    {
        $file = new Doubles\MockedFile();
        $file->name = $filename;
        return $file;
    }
}
