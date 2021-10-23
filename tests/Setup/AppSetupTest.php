<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Setup;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Setup\AppSetup;
use Shudd3r\PackageFiles\Templates;
use Shudd3r\PackageFiles\Replacements;
use Shudd3r\PackageFiles\Exception;
use Shudd3r\PackageFiles\Tests\Doubles;


class AppSetupTest extends TestCase
{
    public function testCreatingReplacementsClassWithGivenReplacementInstances()
    {
        $setup = new AppSetup();
        $setup->addReplacement('foo', new Doubles\FakeReplacement('foo-value'));
        $setup->addReplacement('bar', new Doubles\FakeReplacement('bar-value'));

        $expected = new Replacements([
            'foo' => new Doubles\FakeReplacement('foo-value'),
            'bar' => new Doubles\FakeReplacement('bar-value'),
        ]);
        $this->assertEquals($expected, $setup->replacements());
    }

    public function testOverwritingDefinedReplacement_ThrowsException()
    {
        $setup = new AppSetup();
        $setup->addReplacement('foo', new Doubles\FakeReplacement());

        $this->expectException(Exception\ReplacementOverwriteException::class);
        $setup->addReplacement('foo', new Doubles\FakeReplacement());
    }

    public function testCreatingTemplatesClassWithGivenTemplateFactoryInstances()
    {
        $setup = new AppSetup();
        $setup->addTemplate('file1.txt', new Doubles\FakeTemplateFactory());
        $setup->addTemplate('file2.txt', new Doubles\FakeTemplateFactory());


        $env      = new Doubles\FakeRuntimeEnv();
        $expected = new Templates($env, [
            'file1.txt' => new Doubles\FakeTemplateFactory(),
            'file2.txt' => new Doubles\FakeTemplateFactory(),
        ]);
        $this->assertEquals($expected, $setup->templates($env));
    }

    public function testOverwritingTemplateForDefinedFile_ThrowsException()
    {
        $setup = new AppSetup();
        $setup->addTemplate('file.txt', new Doubles\FakeTemplateFactory());

        $this->expectException(Exception\TemplateOverwriteException::class);
        $setup->addTemplate('file.txt', new Doubles\FakeTemplateFactory());
    }
}
