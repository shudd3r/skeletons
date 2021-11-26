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
use Shudd3r\Skeletons\Processors\Processor\CompareFile;
use Shudd3r\Skeletons\Environment\Files\Directory\VirtualDirectory;
use Shudd3r\Skeletons\Environment\Files\File\VirtualFile;
use Shudd3r\Skeletons\Replacements\Token\BasicToken;
use Shudd3r\Skeletons\Templates\Template;


class CompareFileTest extends TestCase
{
    public function testSuccessfulComparison_ReturnsTrue()
    {
        $template = new Template\BasicTemplate('{replace.me} contents');
        $token    = new BasicToken('replace.me', 'expected');

        $file      = new VirtualFile('foo.txt', 'expected contents');
        $processor = new CompareFile($template, $file);
        $this->assertTrue($processor->process($token));
    }

    public function testFailedComparison_ReturnsFalse()
    {
        $template = new Template\BasicTemplate('{replace.me} contents');
        $token    = new BasicToken('replace.me', 'unexpected');

        $file      = new VirtualFile('foo.txt', 'expected contents');
        $processor = new CompareFile($template, $file);
        $this->assertFalse($processor->process($token));
    }

    public function testInstanceWithBackup_FailedComparison_CreatesCopyOfExistingFile()
    {
        $template = new Template\BasicTemplate('{replace.me} contents');
        $backup   = new VirtualDirectory();
        $token    = new BasicToken('replace.me', 'unexpected');

        $file      = new VirtualFile('foo.txt', null);
        $processor = new CompareFile($template, $file, $backup);
        $this->assertFalse($processor->process($token));
        $this->assertFalse($backup->file('foo.txt')->exists());

        $file      = new VirtualFile('foo.file', 'foo contents');
        $processor = new CompareFile($template, $file, $backup);
        $this->assertFalse($processor->process($token));
        $this->assertTrue($backup->file('foo.file')->exists());
        $this->assertSame('foo contents', $backup->file('foo.file')->contents());
    }

    public function testEmptyTemplate_ForNotExistingFile_ReturnsFalse()
    {
        $template = new Template\BasicTemplate('');
        $file     = new VirtualFile('foo.txt', null);
        $backup   = new VirtualDirectory();
        $token    = new BasicToken('replace.me', 'expected');

        $processor = new CompareFile($template, $file, $backup);
        $this->assertFalse($processor->process($token));
        $this->assertFalse($backup->file('foo.txt')->exists());
    }
}
