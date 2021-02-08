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
        $this->assertSame($expected ?? $original, $token->replace($mask));

        $cache = new Token\TokenCache();
        $this->assertSame($expected ?? $original, $this->token($original, $cache)->replace($mask));
        $this->assertSame($expected ?? $original, $cache->token('cached/file.txt')->replace($mask));
    }

    public function useCases(): array
    {
        $orig = '{' . Token\OriginalContents::PLACEHOLDER . '}';

        $utf         = ['ᚻᛖ ᛒᚢᛞᛖ ᚩᚾ', '⠍⠊⠣⠞ ⠙⠁⠧⠑ ⠃⠑', '😁Hello there!😥', 'Οὐχὶ ταὐτὰ παρίστατ', 'αίგაიቢያዩት ይስቅა', '🌞'];
        $utfContents = implode('', $utf);
        $utfTemplate = $utf[0] . $orig . $utf[2] . $orig . $utf[4] . $orig;

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
            'unicode chars'        => [$utfContents, $utfTemplate, null]
        ];
    }

    private function token(string $originalContents = null, Token\TokenCache $cache = null): Token\OriginalContents
    {
        $file = new Doubles\MockedFile($originalContents);
        $file->name = 'cached/file.txt';

        return $cache
            ? new Token\CachedOriginalContents($file, $cache)
            : new Token\OriginalContents($file);
    }
}
