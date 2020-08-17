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
use Shudd3r\PackageFiles\Token\Reader\NamespaceReader;
use Shudd3r\PackageFiles\Token\Reader\Data;
use Shudd3r\PackageFiles\Token\MainNamespace;
use Shudd3r\PackageFiles\Tests\Doubles;


class NamespaceReaderTest extends TestCase
{
    public function testTokensCreatedFromSingleSource()
    {
        $this->assertEquals(new MainNamespace('Fallback\Namespace'), $this->reader(false, false, false)->token());
        $this->assertEquals(new MainNamespace('Composer\Namespace'), $this->reader(false, false, true)->token());
        $this->assertEquals(new MainNamespace('Option\Namespace'), $this->reader(false, true, false)->token());
        $this->assertEquals(new MainNamespace('Input\Namespace'), $this->reader(true, false, false)->token());
    }

    public function testTokensCreatedFromSourceWithHigherPriority()
    {
        $this->assertEquals(new MainNamespace('Option\Namespace'), $this->reader(false, true, true)->token());
        $this->assertEquals(new MainNamespace('Input\Namespace'), $this->reader(true, true, false)->token());
    }

    private function reader(bool $input, bool $options, bool $composer): NamespaceReader
    {
        $composer = $composer ? ['autoload' => ['psr-4' => ['Composer\\Namespace' => 'src/']]] : [];
        $composer = new Data\ComposerJsonData(new Doubles\MockedFile(json_encode($composer)));
        $fallback = new Doubles\FakeSource('fallback/namespace');
        $input    = new Doubles\MockedTerminal($input ? ['Input\Namespace'] : []);
        $options  = $options ? ['ns' => 'Option\Namespace', 'i' => false] : ['i' => false];

        return new NamespaceReader(new Data\UserInputData($options, $input), $composer, $fallback);
    }
}
