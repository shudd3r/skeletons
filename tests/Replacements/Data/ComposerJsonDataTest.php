<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Replacements\Data;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Replacements\Data\ComposerJsonData;
use Shudd3r\Skeletons\Environment\Files\File\VirtualFile;
use RuntimeException;


class ComposerJsonDataTest extends TestCase
{
    public function testNotJsonData_ThrowsException()
    {
        $composer = new ComposerJsonData(new VirtualFile('not-json.foo', 'some string'));
        $this->expectException(RuntimeException::class);
        $composer->value('foo');
    }

    public function testExtractingValues()
    {
        $composer = $this->composer($data);

        $this->assertSame($data['name'], $composer->value('name'));
        $this->assertSame($data['arrays']['first'], $composer->array('arrays.first'));
        $this->assertSame($data['strings']['value1'], $composer->value('strings.value1'));
    }

    public function testMissingData_ReturnsNull()
    {
        $composer = $this->composer();

        $this->assertNull($composer->value('unknown'));
        $this->assertNull($composer->value('arrays.third'));
        $this->assertNull($composer->value('strings.value3'));
    }

    /**
     * @dataProvider notValueKeys
     * @param string $notValueKey
     */
    public function testNotStringValue_ThrowsException(string $notValueKey)
    {
        $composer = $this->composer();
        $composer->array($notValueKey);
        $this->expectException(RuntimeException::class);
        $composer->value($notValueKey);
    }

    /**
     * @dataProvider notArrayKeys
     * @param string $notArrayKey
     */
    public function testNotArray_ThrowsException(string $notArrayKey)
    {
        $composer = $this->composer();
        $composer->value($notArrayKey);
        $this->expectException(RuntimeException::class);
        $composer->array($notArrayKey);
    }

    /**
     * @dataProvider notValidKeys
     * @param string $notValidKey
     */
    public function testNotValidKey_ThrowsException(string $notValidKey)
    {
        $composer = $this->composer();
        $this->expectException(RuntimeException::class);
        $composer->value($notValidKey);
    }

    public function notValueKeys(): array
    {
        return [['arrays'], ['strings'], ['arrays.first'], ['arrays.second']];
    }

    public function notArrayKeys(): array
    {
        return [['name'], ['strings.value1'], ['strings.value2']];
    }

    public function notValidKeys(): array
    {
        return [['name.something'], ['strings.value1.more']];
    }

    private function composer(array &$data = null): ComposerJsonData
    {
        $data ??= [
            'name' => 'FooName',
            'arrays' => [
                'first' => ['first.value1', 'first.value2'],
                'second' => ['second.value1', 'second.value2'],
            ],
            'strings' => [
                'value1' => 'one',
                'value2' => 'two'
            ]
        ];

        return new ComposerJsonData(new VirtualFile('composer.json', json_encode($data)));
    }
}
