<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Token;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Token;
use Shudd3r\PackageFiles\Tests\Doubles;


class TokenGroupTest extends TestCase
{
    public function testTokenReplacesAllInternalPlaceholders()
    {
        $replace = [
            'FOO' => '{foo.token}',
            'BAR' => '{bar.token}',
            'BAZ' => '{baz.token}'
        ];

        $tokens = [];
        foreach ($replace as $value => $placeholder) {
            $tokens[] = Doubles\FakeToken::withPlaceholder($placeholder, $value);
        }

        $token = new Token\TokenGroup(...$tokens);
        $template = "Template with {$replace['FOO']}-{$replace['BAR']}-{$replace['BAZ']}";

        $this->assertSame('Template with FOO-BAR-BAZ', $token->replacePlaceholders($template));
    }
}
