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
use Shudd3r\PackageFiles\Application\Processor\CompareFile;
use Shudd3r\PackageFiles\Tests\Doubles;


class CompareFileTest extends TestCase
{
    public function testSuccessfulComparison_RendersSuccessMessage()
    {
        $contents  = 'expected contents';
        $template  = new Doubles\FakeTemplate($contents);
        $file      = new Doubles\MockedFile($contents);
        $processor = new CompareFile($template, $file);

        $token = new Doubles\FakeToken();

        $this->assertTrue($processor->process($token));
        $this->assertSame($token, $template->receivedToken);
    }

    public function testFailedComparison_RendersErrorMessage()
    {
        $template  = new Doubles\FakeTemplate('generated contents');
        $file      = new Doubles\MockedFile('expected contents');
        $processor = new CompareFile($template, $file);

        $token = new Doubles\FakeToken();

        $this->assertFalse($processor->process($token));
        $this->assertSame($token, $template->receivedToken);
    }
}
