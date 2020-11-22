<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Token\Reader\Source;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Token;
use Shudd3r\PackageFiles\Tests\Doubles;


class PackageDescriptionTest extends TestCase
{
    /**
     * @dataProvider valueExamples
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testInvalidReaderValue_ReturnsNull(string $invalid, string $valid)
    {
        $this->assertInstanceOf(Token::class, $this->reader()->create($valid));
        $this->assertNull($this->reader()->create($invalid));
    }

    public function valueExamples()
    {
        return [['', 'package description']];
    }

    public function testValue_ReturnsComposerDescription()
    {
        $this->assertSame('composer package description', $this->reader()->value());
    }

    public function testMissingComposerDescription_Value_ReturnsDescriptionFromGivenSource()
    {
        $this->assertSame('package/name package', $this->reader(false)->value());
    }

    private function reader(bool $composerData = true): Token\Reader\Source\PackageDescription
    {
        $contents = json_encode($composerData ? ['description' => 'composer package description'] : []);
        $composer = new Token\Reader\Source\Data\ComposerJsonData(new Doubles\MockedFile($contents));
        return new Token\Reader\Source\PackageDescription($composer, new Doubles\FakeSource('package/name'));
    }
}
