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
use Shudd3r\Skeletons\Setup\ReplacementSetup;
use Shudd3r\Skeletons\Replacements\Replacement;
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

    public function testReplacementSetup_AddsReplacementsInDefinedOrder()
    {
        $appSetup = new AppSetup();

        $setup = new ReplacementSetup($appSetup, 'first.placeholder');
        $setup->add(new Doubles\FakeReplacement(null, null, 'opt1'));

        $setup = new ReplacementSetup($appSetup, 'second.placeholder');
        $setup->build($dummy = fn () => 'dummy')->optionName('opt2');

        $setup = new ReplacementSetup($appSetup, 'third.placeholder');
        $setup->add(new Doubles\FakeReplacement(null, null, 'opt3'));

        $replacements = $appSetup->replacements();
        $expectedReplacement = new Replacement\GenericReplacement($dummy, null, null, null, 'opt2');
        $this->assertEquals($expectedReplacement, $replacements->replacement('second.placeholder'));

        $placeholderOrder = array_keys($replacements->info());
        $this->assertSame(['first.placeholder', 'second.placeholder', 'third.placeholder'], $placeholderOrder);
    }

    public function testReplacementSetupBuildForExistingPlaceholder_ThrowsException()
    {
        $appSetup = new AppSetup();

        $setup = new ReplacementSetup($appSetup, 'first.placeholder');
        $setup->add(new Doubles\FakeReplacement());

        $setup = new ReplacementSetup($appSetup, 'first.placeholder');

        $this->expectException(Exception\ReplacementOverwriteException::class);
        $setup->build(fn () => 'dummy');
    }

    public function testReplacementSetupBuildForBuiltInPlaceholder_ThrowsException()
    {
        $appSetup = new AppSetup();
        $setup    = new ReplacementSetup($appSetup, 'original.content');

        $this->expectException(Exception\ReplacementOverwriteException::class);
        $setup->build(fn () => 'dummy');
    }
}
