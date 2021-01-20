<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Token\Source;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Token\Source\MetaDataFile;
use Shudd3r\PackageFiles\Application\Token\Source\Data\SavedPlaceholderValues;
use Shudd3r\PackageFiles\Tests\Doubles;
use RuntimeException;


class MetaDataFileTest extends TestCase
{
    public function testMissingMetaDataFile_ThrowsException()
    {
        $source = $this->source('placeholder.name', null);
        $this->expectException(RuntimeException::class);
        $source->value();
    }

    public function testWithExistingMetaDataValue_ValueMethod_ReturnsMetaData()
    {
        $source = $this->source('foo', ['foo' => 'meta data']);
        $this->assertSame('meta data', $source->value());
    }

    public function testValueMethod_ReturnsValueAssociatedWithGivenName()
    {
        $source = $this->source('two', [
            'one' => 'first value',
            'two' => 'second value'
        ]);

        $this->assertSame('second value', $source->value());
    }

    public function testWithoutMetaDataValue_ValueMethod_ReturnsFromFallbackSource()
    {
        $source = $this->source('another.name', ['placeholder.name' => 'bar']);
        $this->assertSame('fallback', $source->value());
    }

    private function source(string $name, ?array $data): MetaDataFile
    {
        $metaFile = new Doubles\MockedFile(isset($data) ? json_encode($data) : null);
        $data = new SavedPlaceholderValues($metaFile);
        return new MetaDataFile($name, $data, new Doubles\FakeSource('fallback'));
    }
}
