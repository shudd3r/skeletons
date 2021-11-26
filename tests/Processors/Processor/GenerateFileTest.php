<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Processors\Processor;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Processors\Processor\GenerateFile;
use Shudd3r\Skeletons\Replacements\Token\BasicToken;
use Shudd3r\Skeletons\Templates\Template;
use Shudd3r\Skeletons\Environment\Files\File;


class GenerateFileTest extends TestCase
{
    public function testRenderedStringIsWrittenToFile()
    {
        $template  = new Template\BasicTemplate('{replace.me} string');
        $file      = new File\VirtualFile();
        $processor = new GenerateFile($template, $file);

        $this->assertTrue($processor->process(new BasicToken('replace.me', 'rendered')));
        $this->assertSame('rendered string', $file->contents());
    }
}
