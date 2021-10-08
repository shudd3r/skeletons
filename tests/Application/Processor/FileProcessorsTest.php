<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Processor;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Tests\Doubles;


class FileProcessorsTest extends TestCase
{
    public function testWithoutDefinedCustomTemplate_ProcessorUsesGenericTemplate()
    {
        $custom     = new Template\BasicTemplate('render');
        $templates  = new Template\Factory\Templates(['myFile.txt' => new Doubles\FakeTemplateFactory($custom)]);
        $processors = new Doubles\MockedFileProcessors(new Doubles\FakeDirectory(), $templates);

        $file = new Doubles\MockedFile('', 'differentFile.txt');
        $processors->processor($file);

        $this->assertNotEquals($custom, $processors->usedTemplate());
    }

    public function testWithDefinedCustomTemplate_ProcessorUsesThisTemplate()
    {
        $custom     = new Template\BasicTemplate('render');
        $templates  = new Template\Factory\Templates(['myFile.txt' => new Doubles\FakeTemplateFactory($custom)]);
        $processors = new Doubles\MockedFileProcessors(new Doubles\FakeDirectory(), $templates);

        $file = new Doubles\MockedFile('', 'myFile.txt');
        $processors->processor($file);

        $this->assertSame($custom, $processors->usedTemplate());
    }
}