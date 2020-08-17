<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Token\Reader;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Token\Reader\PackageReader;
use Shudd3r\PackageFiles\Token\Reader\Data;
use Shudd3r\PackageFiles\Token\Package;
use Shudd3r\PackageFiles\Tests\Doubles;


class PackageReaderTest extends TestCase
{
    public function testTokensCreatedFromSingleSource()
    {
        $this->assertEquals(new Package('directory/package'), $this->reader(false, false, false)->token());
        $this->assertEquals(new Package('composer/package'), $this->reader(false, false, true)->token());
        $this->assertEquals(new Package('option/package'), $this->reader(false, true, false)->token());
        $this->assertEquals(new Package('input/package'), $this->reader(true, false, false)->token());
    }

    public function testTokensCreatedFromSourceWithHigherPriority()
    {
        $this->assertEquals(new Package('option/package'), $this->reader(false, true, true)->token());
        $this->assertEquals(new Package('input/package'), $this->reader(true, true, false)->token());
    }

    private function reader(bool $input, bool $options, bool $composer): PackageReader
    {
        $composer  = $composer ? ['name' => 'composer/package'] : [];
        $composer  = new Data\ComposerJsonData(new Doubles\MockedFile(json_encode($composer)));
        $directory = new Doubles\FakeDirectory(true, '/foo/bar/directory/package');
        $input     = new Doubles\MockedTerminal($input ? ['input/package'] : []);
        $options   = $options ? ['package' => 'option/package', 'i' => false] : ['i' => false];

        return new PackageReader(new Data\UserInputData($options, $input), $composer, $directory);
    }
}
