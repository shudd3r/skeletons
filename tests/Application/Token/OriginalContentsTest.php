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
     * @param string  $original
     * @param string  $mask
     * @param ?string $expected
     */
    public function testUseCases(string $original, string $mask, ?string $expected)
    {
        $token = $this->token($original);
        $this->assertSame($expected ?? $original, $token->replacePlaceholders($mask));

        $cache = new Token\FilesTokenCache();
        $token = $this->token($original, $cache);
        $this->assertSame($expected ?? $original, $token->replacePlaceholders($mask));

        $cachedToken = $cache->token($this->file('cached/file.txt'));
        $this->assertSame($expected ?? $original, $cachedToken->replacePlaceholders($mask));
    }

    public function useCases(): array
    {
        $orig = Token\OriginalContents::PLACEHOLDER;
        return [
            'no placeholder'       => ['original contents', 'template string', 'template string'],
            'no original contents' => ['', "template -{$orig}- string", 'template -- string'],
            'only placeholder'     => ['This is original content', "{$orig}", null],
            'surrounded by text'   => ['Foo -Bar- Baz', "Foo -{$orig}- Baz", null],
            'two placeholders'     => ['Foo -Bar- Baz', "Foo -{$orig}- {$orig}", null],
            'three placeholders'   => ['Foo -Bar- Baz', "{$orig} -{$orig}- {$orig}", null],
            'repeated content'     => ['FooBarFooBarFoo', "{$orig}Bar{$orig}BarFoo", null],
            'extremely repeated'   => ['xxxxx', "x{$orig}x{$orig}x", null],
            'mask mismatch'        => ['---ab---', "123{$orig}=string={$orig}123", '123ab=string=123'],
        ];
    }

    private function token(string $originalContents = null, Token\FilesTokenCache $cache = null): Token\OriginalContents
    {
        $file = $this->file('cached/file.txt', $originalContents);

        return $cache
            ? new Token\OriginalContents($file, $cache)
            : new Token\OriginalContents($file);
    }

    private function file(string $filename, string $contents = ''): Doubles\MockedFile
    {
        $file = new Doubles\MockedFile($contents);
        $file->name = $filename;
        return $file;
    }
}
