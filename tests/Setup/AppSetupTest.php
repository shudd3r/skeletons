<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Setup;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Setup\AppSetup;
use Shudd3r\Skeletons\Exception;
use Shudd3r\Skeletons\Tests\Doubles;


class AppSetupTest extends TestCase
{
    public function testOverwritingDefinedReplacement_ThrowsException()
    {
        $setup = new AppSetup();
        $setup->addReplacement('foo', new Doubles\FakeReplacement());

        $this->expectException(Exception\ReplacementOverwriteException::class);
        $setup->addReplacement('foo', new Doubles\FakeReplacement());
    }

    public function testOverwritingBuiltInReplacement_ThrowsException()
    {
        $setup = new AppSetup();

        $this->expectException(Exception\ReplacementOverwriteException::class);
        $setup->addReplacement('original.content', new Doubles\FakeReplacement());
    }

    public function testOverwritingTemplateForDefinedFile_ThrowsException()
    {
        $setup = new AppSetup();
        $setup->addTemplate('file.txt', new Doubles\FakeTemplateFactory());

        $this->expectException(Exception\TemplateOverwriteException::class);
        $setup->addTemplate('file.txt', new Doubles\FakeTemplateFactory());
    }
}
