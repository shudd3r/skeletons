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
use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Application\Token;
use Shudd3r\PackageFiles\Tests\Doubles;


class CompareFileTest extends TestCase
{
    public function testSuccessfulComparison_ReturnsTrue()
    {
        $template  = new Template\BasicTemplate('{replace.me} contents');
        $file      = new Doubles\MockedFile('expected contents');
        $processor = new CompareFile($template, $file);

        $token = new Token\ValueToken('replace.me', 'expected');
        $this->assertTrue($processor->process($token));
    }

    public function testFailedComparison_ReturnsFalse()
    {
        $template  = new Template\BasicTemplate('{replace.me} contents');
        $file      = new Doubles\MockedFile('expected contents');
        $processor = new CompareFile($template, $file);

        $token = new Token\ValueToken('replace.me', 'unexpected');
        $this->assertFalse($processor->process($token));
    }
}
