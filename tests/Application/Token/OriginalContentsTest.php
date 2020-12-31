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
use Shudd3r\PackageFiles\Application\Token;
use Shudd3r\PackageFiles\Tests\Doubles;


class OriginalContentsTest extends TestCase
{
    /**
     * @dataProvider useCases
     * @param string $original
     * @param string $mask
     * @param array  $expected
     */
    public function testUseCases(string $original, string $mask, array $expected)
    {
        $this->assertEquals($this->token($expected), $this->contents($original)->token($mask));
    }

    public function useCases(): array
    {
        $orig = Token\OriginalContents::PLACEHOLDER;
        return [
            'no placeholder'       => ['original contents', 'template string', []],
            'no original contents' => ['', "template -{$orig}- string", []],
            'only placeholder'     => ['This is original content', "{$orig}", ['This is original content']],
            'surrounded by text'   => ['Foo -Bar- Baz', "Foo -{$orig}- Baz", ['Bar']],
            'two placeholders'     => ['Foo -Bar- Baz', "Foo -{$orig}- {$orig}", ['Bar','Baz']],
            'three placeholders'   => ['Foo -Bar- Baz', "{$orig} -{$orig}- {$orig}", ['Foo', 'Bar', 'Baz']],
            'repeated content'     => ['FooBarFooBarFoo', "{$orig}Bar{$orig}BarFoo", ['Foo', 'Foo']],
            'extremely repeated'   => ['xxxxx', "x{$orig}x{$orig}x", ['', 'xx']],
            'mask mismatch'        => ['-----', "x{$orig}x{$orig}x", ['---', '']]
        ];
    }

    private function contents(string $originalContents = null): Token\OriginalContents
    {
        return new Token\OriginalContents(new Doubles\MockedFile($originalContents));
    }

    private function token(array $values): Token
    {
        $placeholder = Token\OriginalContents::PLACEHOLDER;
        if (!$values) { return new Token\ValueToken($placeholder, ''); }

        return count($values) === 1
            ? new Token\ValueToken($placeholder, $values[0])
            : new Token\ValueListToken($placeholder, ...$values);
    }
}
