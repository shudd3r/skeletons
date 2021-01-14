<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Token\ReaderFactory;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Token\ReaderFactory\RepositoryNameReaderFactory;
use Shudd3r\PackageFiles\Application\Token\ReaderFactory\PackageNameReaderFactory;
use Shudd3r\PackageFiles\Tests\Doubles;


class RepositoryNameReaderFactoryTest extends TestCase
{
    /**
     * @dataProvider valueExamples
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testReaderValueValidation(string $invalid, string $valid)
    {
        $replacement = $this->replacement();
        $this->assertTrue($replacement->isValid($valid));
        $this->assertFalse($replacement->isValid($invalid));
    }

    public function valueExamples()
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

    private function replacement(): RepositoryNameReaderFactory
    {
        $env     = new Doubles\FakeRuntimeEnv();
        $package = new PackageNameReaderFactory($env, []);
        return new RepositoryNameReaderFactory($env, [], $package);
    }
}
