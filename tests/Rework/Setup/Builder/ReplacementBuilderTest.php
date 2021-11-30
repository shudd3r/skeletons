<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Rework\Setup\Builder;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Rework\Setup\Builder;
use Shudd3r\Skeletons\Rework\Replacements\Replacement;


class ReplacementBuilderTest extends TestCase
{
    /**
     * @dataProvider constructorParams
     * @param array $params
     */
    public function testBuildingReplacement(array $params)
    {
        $builder = new Builder\ReplacementBuilder($params[0]);
        $setup   = new Builder\BuildSetup($builder);

        if ($params[1] ?? null) { $setup->validate($params[1]); }
        if ($params[2] ?? null) { $setup->token($params[2]); }
        if ($params[3] ?? null) { $setup->inputPrompt($params[3]); }
        if ($params[4] ?? null) { $setup->argumentName($params[4]); }
        if ($params[5] ?? null) { $setup->description($params[5]); }

        $expected = new Replacement\GenericReplacement(...$params);
        $this->assertEquals($expected, $builder->build());
    }

    public function constructorParams(): array
    {
        $dummy = fn (int $value) => fn () => $value;

        return [
            [[$dummy(1)]],
            [[$dummy(1), $dummy(2), $dummy(3), 'prompt', 'option', 'description']],
            [[$dummy(1), null, $dummy(3), 'prompt', null, 'description']],
            [[$dummy(1), null, null, null, null, 'description']],
            [[$dummy(1), $dummy(2), null, null, 'opt', 'description']],
            [[$dummy(1), $dummy(2), null, 'prompt']]
        ];
    }
}
