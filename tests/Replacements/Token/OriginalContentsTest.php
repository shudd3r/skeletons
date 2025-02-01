<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Replacements\Token;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Replacements\Token;
use Shudd3r\Skeletons\Replacements\TokenCache;


class OriginalContentsTest extends TestCase
{
    private const FILENAME = 'cached/file.txt';

    public function test_value_method_returns_null(): void
    {
        $token = $this->token('foo');
        $this->assertNull($token->value());
    }

    /** @dataProvider useCases */
    public function test_use_cases(string $original, string $mask, ?string $expected): void
    {
        $token = $this->token($original);
        $this->assertSame($expected ?? $original, $token->replace($mask));

        $cache = new TokenCache();
        $token = $this->token($original, $cache);
        $this->assertSame($expected ?? $original, $token->replace($mask));
        $this->assertSame($expected ?? $original, $cache->token(self::FILENAME)->replace($mask));
    }

    public static function useCases(): array
    {
        $orig = '{' . Token\OriginalContents::PLACEHOLDER . '}';

        $utf         = ['ᚻᛖ ᛒᚢᛞᛖ ᚩᚾ', '⠍⠊⠣⠞ ⠙⠁⠧⠑ ⠃⠑', '😁Hello there!😥', 'Οὐχὶ ταὐτὰ παρίστατ', 'αίგაიቢያዩት ይስቅა', '🌞'];
        $utfContents = implode('', $utf);
        $utfTemplate = $utf[0] . $orig . $utf[2] . $orig . $utf[4] . $orig;

        return [
            'no placeholder'       => ['original contents', 'template string', 'template string'],
            'no original contents' => ['', "template -{$orig}- string", 'template -- string'],
            'short contents'       => ['xxx', "template -{$orig}- string", 'template -- string'],
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

    private function token(string $contents, ?TokenCache $cache = null): Token\OriginalContents
    {
        return $cache
            ? new Token\CachedOriginalContents($contents, self::FILENAME, $cache)
            : new Token\OriginalContents($contents);
    }
}
