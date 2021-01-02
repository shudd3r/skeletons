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
use Shudd3r\PackageFiles\Application\Token\ValueListToken;
use RuntimeException;


class ValueListTokenTest extends TestCase
{
    public function testPlaceholdersAreReplacedWithConsecutiveValues()
    {
        $template = '{replace}=1 {replace}=2 {replace}=3 {replace}=4';
        $token    = $this->token(['one', 'two', 'three', 'four']);
        $this->assertSame('one=1 two=2 three=3 four=4', $token->replacePlaceholders($template));
    }

    /**
     * @dataProvider mismatchedPlaceholders
     * @param string $template
     * @param array $values
     */
    public function testDifferentNumberOfPlaceholders_ThrowsException(string $template, array $values)
    {
        $token = $this->token($values);
        $this->expectException(RuntimeException::class);
        $token->replacePlaceholders($template);
    }

    public function mismatchedPlaceholders(): array
    {
        return [
            ['this {replace} has {replace} placeholders {replace}', ['one', 'three']],
            ['this {replace} has only {replace} placeholders', ['one', 'two', 'three']],
        ];
    }

    private function token(array $values): ValueListToken
    {
        return new ValueListToken('{replace}', ...$values);
    }
}