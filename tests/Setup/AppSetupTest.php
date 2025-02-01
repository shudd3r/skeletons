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
    public function test_templates_method_returns_templates_with_indexed_skeleton_files(): void
    {
        $filenames = ['basic.file', 'escaped.file', 'escaped/local.file', 'dir/initial.file', 'src/.gitkeep'];
        $package   = $this->directoryFiles($filenames);
        $template  = $this->directoryFiles([
            'basic.file', 'escaped.file.sk_file',
            'dir/initial.file.sk_init', 'src/.gitkeep',
            'escaped.sk_dir/local.file.sk_local'
        ]);

        $setup     = new AppSetup();
        $templates = $setup->templates(new Doubles\FakeRuntimeEnv($package, $template));

        $files = $templates->generatedFiles(new InputArgs(['script', 'init', '--local']));
        $this->assertFileList($files, $filenames, ['src/.gitkeep']);

        $files = $templates->generatedFiles(new InputArgs(['script', 'init']));
        $this->assertFileList($files, $filenames, ['src/.gitkeep', 'escaped/local.file']);

        $files = $templates->generatedFiles(new InputArgs(['script', 'check', '--local']));
        $this->assertFileList($files, ['basic.file', 'escaped.file', 'escaped/local.file']);

        $files = $templates->generatedFiles(new InputArgs(['script', 'check']));
        $this->assertFileList($files, ['basic.file', 'escaped.file']);

        $this->assertFileList($templates->dummyFiles(), ['src/.gitkeep']);
    }

    public function test_removing_template_generated_files_removes_files_from_package_directory(): void
    {
        $filenames = ['basic.file', 'escaped.file', 'escaped/local.file', 'dir/initial.file', 'src/.gitkeep'];
        $package   = $this->directoryFiles($filenames);
        $template  = $this->directoryFiles([
            'basic.file', 'escaped.file.sk_file',
            'dir/initial.file.sk_init', 'src/.gitkeep',
            'escaped.sk_dir/local.file.sk_local'
        ]);

        $setup     = new AppSetup();
        $templates = $setup->templates(new Doubles\FakeRuntimeEnv($package, $template));
        $generated = $templates->generatedFiles(new InputArgs(['script', 'init', '--local']))->fileList();

        $this->assertFileList($package, $filenames);
        array_walk($generated, fn (File $file) => $file->remove());
        $this->assertFileList($package, ['src/.gitkeep']);
    }

    public function test_replacement_setup_adds_replacements_in_order_of_their_definition(): void
    {
        $setup = new AppSetup();

        $replacement = new ReplacementSetup($setup, 'first.placeholder');
        $replacement->add(Doubles\FakeReplacement::create()->withInputArg('opt1'));

        $replacement = new ReplacementSetup($setup, 'second.placeholder');
        $replacement->build($dummy = fn () => 'dummy')->argumentName('opt2');

        $replacement = new ReplacementSetup($setup, 'third.placeholder');
        $replacement->add(Doubles\FakeReplacement::create()->withInputArg('opt3'));

        $replacements = $setup->replacements();
        $expectedReplacement = new Replacement\GenericReplacement($dummy, null, null, null, 'opt2');
        $this->assertEquals($expectedReplacement, $replacements->replacement('second.placeholder'));

        $placeholderOrder = ['opt1', 'opt2', 'opt3'];
        foreach ($replacements->info() as $argumentDescription) {
            $argument = trim(substr($argumentDescription, 0, 14));
            $this->assertSame(array_shift($placeholderOrder), $argument);
        }
    }

    public function test_overwriting_replacement_definition_throws_Exception(): void
    {
        $setup = new AppSetup();

        $setup->addReplacement('foo', new Doubles\FakeReplacement());
        $this->expectException(Exception\ReplacementOverwriteException::class);
        $setup->addReplacement('foo', new Doubles\FakeReplacement());
    }

    public function test_overwriting_built_in_replacement_throws_Exception(): void
    {
        $setup = new AppSetup();

        $this->expectException(Exception\ReplacementOverwriteException::class);
        $setup->addReplacement('original.content', new Doubles\FakeReplacement());
    }

    public function test_overwriting_template_for_defined_file_throws_Exception(): void
    {
        $setup = new AppSetup();

        $setup->addTemplate('file.txt', fn () => null);
        $this->expectException(Exception\TemplateOverwriteException::class);
        $setup->addTemplate('file.txt', fn () => null);
    }

    public function test_replacement_setup_build_for_existing_placeholder_throws_Exception(): void
    {
        $setup = new AppSetup();

        $replacement = new ReplacementSetup($setup, 'first.placeholder');
        $replacement->add(new Doubles\FakeReplacement());

        $replacement = new ReplacementSetup($setup, 'first.placeholder');
        $this->expectException(Exception\ReplacementOverwriteException::class);
        $replacement->build(fn () => 'dummy');
    }

    public function test_replacement_setup_build_for_built_in_placeholder_throws_Exception(): void
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
