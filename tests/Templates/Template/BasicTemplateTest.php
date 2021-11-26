<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Templates\Template;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Templates\Template\BasicTemplate;
use Shudd3r\Skeletons\Replacements\Token\BasicToken;


class BasicTemplateTest extends TestCase
{
    public function testPlaceholders_AreReplacedByTokenValues()
    {
        $contents = <<<'TPL'
            This file is part of {placeholder.name} package.
            TPL;

        $template = new BasicTemplate($contents);
        $token    = new BasicToken('placeholder.name', 'package/name');

        $render = $template->render($token);

        $expected = <<<'RENDER'
            This file is part of package/name package.
            RENDER;

        $this->assertSame($expected, $render);
    }
}
