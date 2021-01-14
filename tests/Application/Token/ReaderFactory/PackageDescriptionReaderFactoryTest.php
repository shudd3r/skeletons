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
use Shudd3r\PackageFiles\Application\Token\ReaderFactory\PackageDescriptionReaderFactory;
use Shudd3r\PackageFiles\Application\Token\ReaderFactory\PackageNameReaderFactory;
use Shudd3r\PackageFiles\Tests\Doubles;


class PackageDescriptionReaderFactoryTest extends TestCase
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
        return [['', 'package description']];
    }

    private function replacement(): PackageDescriptionReaderFactory
    {
        $env     = new Doubles\FakeRuntimeEnv();
        $package = new PackageNameReaderFactory($env, []);
        return new PackageDescriptionReaderFactory($env, [], $package);
    }
}
