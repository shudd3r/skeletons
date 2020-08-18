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
use Shudd3r\PackageFiles\Token\Reader\DescriptionReader;
use Shudd3r\PackageFiles\Token\Reader\Data;
use Shudd3r\PackageFiles\Token\Description;
use Shudd3r\PackageFiles\Tests\Doubles;


class DescriptionReaderTest extends TestCase
{
    public function testTokensCreatedFromSingleSource()
    {
        $this->assertEquals(new Description('Fallback package'), $this->reader(false, false, false)->token());
        $this->assertEquals(new Description('composer'), $this->reader(false, false, true)->token());
        $this->assertEquals(new Description('option'), $this->reader(false, true, false)->token());
        $this->assertEquals(new Description('input'), $this->reader(true, false, false)->token());
    }

    public function testTokensCreatedFromSourceWithHigherPriority()
    {
        $this->assertEquals(new Description('option'), $this->reader(false, true, true)->token());
        $this->assertEquals(new Description('input'), $this->reader(true, true, false)->token());
    }

    private function reader(bool $input, bool $options, bool $composer): DescriptionReader
    {
        $composer = $composer ? ['description' => 'composer'] : [];
        $composer = new Data\ComposerJsonData(new Doubles\MockedFile(json_encode($composer)));
        $options  = $options ? ['desc' => 'option', 'i' => false] : ['i' => false];
        $input    = new Doubles\MockedTerminal($input ? ['input'] : []);
        $input    = new Data\UserInputData($options, $input);
        $fallback = new Doubles\FakeValueReader($input, 'Fallback');

        return new DescriptionReader($input, $composer, $fallback);
    }
}
