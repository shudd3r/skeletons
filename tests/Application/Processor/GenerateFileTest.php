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
use Shudd3r\PackageFiles\Application\Processor\GenerateFile;
use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Application\Token;
use Shudd3r\PackageFiles\Tests\Doubles;


class GenerateFileTest extends TestCase
{
    public function testRenderedStringIsWrittenToFile()
    {
        $template  = new Template\BasicTemplate('{replace.me} string');
        $file      = new Doubles\MockedFile();
        $processor = new GenerateFile($template, $file);

        $token = new Token\ValueToken('replace.me', 'rendered');
        $this->assertTrue($processor->process($token));
        $this->assertSame('rendered string', $file->contents());
    }
}
