<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Setup\Builder;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Setup\Builder\ReplacementBuilder;
use Shudd3r\Skeletons\Setup\Builder\BuildSetup;
use Shudd3r\Skeletons\Replacements\Replacement;


class ReplacementBuilderTest extends TestCase
{
    /**
     * @dataProvider constructorParams
     * @param array $constructorParams
     */
    public function testBuildingReplacement(array $constructorParams)
    {
        $builder = new ReplacementBuilder($constructorParams[0]);
        $setup   = new BuildSetup($builder);

        if ($constructorParams[1] ?? null) { $setup->token($constructorParams[1]); }
        if ($constructorParams[2] ?? null) { $setup->validate($constructorParams[2]); }
        if ($constructorParams[3] ?? null) { $setup->inputPrompt($constructorParams[3]); }
        if ($constructorParams[4] ?? null) { $setup->optionName($constructorParams[4]); }
        if ($constructorParams[5] ?? null) { $setup->description($constructorParams[5]); }

        $expected = new Replacement\GenericReplacement(...$constructorParams);
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
