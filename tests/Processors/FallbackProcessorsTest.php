<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Processors;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Processors\FallbackProcessors;
use Shudd3r\Skeletons\Processors\Processor\FallbackProcessor;
use Shudd3r\Skeletons\Templates\Template\BasicTemplate;
use Shudd3r\Skeletons\Environment\Files\File\VirtualFile;
use Shudd3r\Skeletons\Tests\Doubles;


class FallbackProcessorsTest extends TestCase
{
    public function testProcessorMethod_ReturnsFallbackProcessor()
    {
        $primary  = new Doubles\MockedProcessors();
        $fallback = new Doubles\MockedProcessors();
        $this->assertNull($primary->createdProcessor());
        $this->assertNull($fallback->createdProcessor());

        $processors = new FallbackProcessors($primary, $fallback);
        $processor  = $processors->processor(new BasicTemplate(''), new VirtualFile());
        $expected   = new FallbackProcessor($primary->createdProcessor(), $fallback->createdProcessor());
        $this->assertEquals($expected, $processor);
    }
}
