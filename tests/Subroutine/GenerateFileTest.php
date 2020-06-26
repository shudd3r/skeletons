<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Subroutine;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Subroutine\GenerateFile;
use Shudd3r\PackageFiles\Tests\Doubles;


class GenerateFileTest extends TestCase
{
    public function testRenderedStringIsWrittenToFile()
    {
        $template   = new Doubles\FakeTemplate($rendered = 'rendered string');
        $fileMock   = new Doubles\MockedFile();
        $subroutine = new GenerateFile($template, $fileMock);

        $subroutine->process($properties = new Doubles\FakeProperties());

        $this->assertSame($properties, $template->receivedProperties);
        $this->assertSame($rendered, $fileMock->contents);
    }
}
