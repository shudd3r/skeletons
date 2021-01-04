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
use Shudd3r\PackageFiles\Tests\Doubles;
use RuntimeException;


class MetaDataFileTest extends TestCase
{
    public function testMissingMetaDataFile_ThrowsException()
    {
        $source = $this->source(null);
        $this->expectException(RuntimeException::class);
        $source->value(new Doubles\FakeValidator());
    }

    public function testWithExistingMetaDataValue_ValueMethod_ReturnsMetaData()
    {
        $source = $this->source([Doubles\FakeValidator::class => 'meta data']);
        $this->assertSame('meta data', $source->value(new Doubles\FakeValidator()));
    }

    public function testValueMethod_ReturnsValueAssociatedWithGivenParser()
    {
        $source = $this->source([
            Doubles\FakeValidator::class     => 'first value',
            Doubles\MockedValueReader::class => 'second value'
        ]);

        $this->assertSame('first value', $source->value(new Doubles\FakeValidator()));
        $this->assertSame('second value', $source->value(new Doubles\MockedValueReader()));
    }

    public function testWithoutMetaDataValue_ValueMethod_ReturnsFromFallbackSource()
    {
        $source = $this->source([Doubles\MockedValueReader::class => 'bar']);
        $this->assertSame('fallback', $source->value(new Doubles\FakeValidator()));
    }

    private function source(?array $data): MetaDataFile
    {
        $metaFile = new Doubles\MockedFile(isset($data) ? json_encode($data) : null);
        return new MetaDataFile($metaFile, new Doubles\FakeSource('fallback'));
    }
}
