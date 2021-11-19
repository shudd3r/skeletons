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
use Shudd3r\Skeletons\Environment\Files\Directory;
use Shudd3r\Skeletons\Environment\Files\File;
use Shudd3r\Skeletons\Environment\Files;
use Shudd3r\Skeletons\InputArgs;
use Shudd3r\Skeletons\Exception;
use Shudd3r\Skeletons\Tests\Doubles;


class AppSetupTest extends TestCase
{
    public function testTemplates_ReturnsTemplatesWithIndexedSkeletonFiles()
    {
        $filenames = ['basic.file', 'escaped.file', 'escaped/local.file', 'dir/initial.file', 'src/dummy.file'];
        $package   = $this->directoryFiles($filenames);
        $template  = $this->directoryFiles([
            'basic.file', 'escaped.file.sk_file',
            'dir/initial.file.sk_init', 'src/dummy.file.sk_dummy',
            'escaped.sk_dir/local.file.sk_local'
        ]);

        $setup     = new AppSetup();
        $templates = $setup->templates(new Doubles\FakeRuntimeEnv($package, $template));

        $files = $templates->generatedFiles(new InputArgs(['script', 'init', '--local']));
        $this->assertFileList($files, $filenames, ['src/dummy.file']);

        $files = $templates->generatedFiles(new InputArgs(['script', 'init']));
        $this->assertFileList($files, $filenames, ['src/dummy.file', 'escaped/local.file']);

        $files = $templates->generatedFiles(new InputArgs(['script', 'check', '--local']));
        $this->assertFileList($files, ['basic.file', 'escaped.file', 'escaped/local.file']);

        $files = $templates->generatedFiles(new InputArgs(['script', 'check']));
        $this->assertFileList($files, ['basic.file', 'escaped.file']);

        $this->assertFileList($templates->dummyFiles(), ['src/dummy.file']);
    }

    public function testRemovingTemplateGeneratedFiles_RemovesFilesFromPackageDirectory()
    {
        $filenames = ['basic.file', 'escaped.file', 'escaped/local.file', 'dir/initial.file', 'src/dummy.file'];
        $package   = $this->directoryFiles($filenames);
        $template  = $this->directoryFiles([
            'basic.file', 'escaped.file.sk_file',
            'dir/initial.file.sk_init', 'src/dummy.file.sk_dummy',
            'escaped.sk_dir/local.file.sk_local'
        ]);

        $setup     = new AppSetup();
        $templates = $setup->templates(new Doubles\FakeRuntimeEnv($package, $template));
        $generated = $templates->generatedFiles(new InputArgs(['script', 'init', '--local']))->fileList();

        $this->assertFileList($package, $filenames);
        array_walk($generated, fn (File $file) => $file->remove());
        $this->assertFileList($package, ['src/dummy.file']);
    }

    public function testReplacementSetup_AddsReplacementsInDefinedOrder()
    {
        $setup = new AppSetup();

        $replacement = new ReplacementSetup($setup, 'first.placeholder');
        $replacement->add(new Doubles\FakeReplacement(null, null, 'opt1'));

        $replacement = new ReplacementSetup($setup, 'second.placeholder');
        $replacement->build($dummy = fn () => 'dummy')->optionName('opt2');

        $replacement = new ReplacementSetup($setup, 'third.placeholder');
        $replacement->add(new Doubles\FakeReplacement(null, null, 'opt3'));

        $replacements = $setup->replacements();
        $expectedReplacement = new Replacement\GenericReplacement($dummy, null, null, null, 'opt2');
        $this->assertEquals($expectedReplacement, $replacements->replacement('second.placeholder'));

        $placeholderOrder = array_keys($replacements->info());
        $this->assertSame(['first.placeholder', 'second.placeholder', 'third.placeholder'], $placeholderOrder);
    }

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

    public function testReplacementSetupBuildForExistingPlaceholder_ThrowsException()
    {
        $setup = new AppSetup();

        $replacement = new ReplacementSetup($setup, 'first.placeholder');
        $replacement->add(new Doubles\FakeReplacement());

        $replacement = new ReplacementSetup($setup, 'first.placeholder');
        $this->expectException(Exception\ReplacementOverwriteException::class);
        $replacement->build(fn () => 'dummy');
    }

    public function testReplacementSetupBuildForBuiltInPlaceholder_ThrowsException()
    {
        $setup = new AppSetup();

        $replacement = new ReplacementSetup($setup, 'original.content');
        $this->expectException(Exception\ReplacementOverwriteException::class);
        $replacement->build(fn () => 'dummy');
    }

    private function assertFileList(Files $files, array $filenames, array $except = []): void
    {
        $filenames = array_flip($except ? array_diff($filenames, $except) : $filenames);
        foreach ($files->fileList() as $file) {
            $name = $file->name();
            $this->assertArrayHasKey($name, $filenames);
            unset($filenames[$name]);
        }

        $this->assertEmpty($filenames);
    }

    private function directoryFiles(array $filenames): Directory\VirtualDirectory
    {
        $directory = new Directory\VirtualDirectory();
        array_walk($filenames, fn (string $filename) => $directory->addFile($filename));
        return $directory;
    }
}
