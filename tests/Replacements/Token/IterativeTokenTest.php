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
use Shudd3r\Skeletons\Replacements\Token\IterativeToken;
use RuntimeException;


class IterativeTokenTest extends TestCase
{
    public function testValueMethod_ReturnsNull()
    {
        $token = $this->token(['foo', 'bar']);
        $this->assertNull($token->value());
    }

    public function testPlaceholdersAreReplacedWithConsecutiveValues()
    {
        $template = '{replace}=1 {replace}=2 {replace}=3 {replace}=4';
        $token    = $this->token(['one', 'two', 'three', 'four']);
        $this->assertSame('one=1 two=2 three=3 four=4', $token->replace($template));
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
        $token->replace($template);
    }

    public function mismatchedPlaceholders(): array
    {
        return [
            ['this {replace} has {replace} placeholders {replace}', ['one', 'three']],
            ['this {replace} has only {replace} placeholders', ['one', 'two', 'three']],
        ];
    }

    private function token(array $values): IterativeToken
    {
        return new IterativeToken('replace', ...$values);
    }
}
