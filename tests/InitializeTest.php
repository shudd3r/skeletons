<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Initialize;
use Shudd3r\PackageFiles\Environment\Command;


class InitializeTest extends TestCase
{
    public function testFactoryCreatesCommand()
    {
        $factory = new Initialize(new Doubles\FakeRuntimeEnv(), []);
        $this->assertInstanceOf(Command::class, $factory->command());
    }

    public function testDefaultTokenValues_AreReadFromPackageFiles()
    {
        $setup     = new EnvSetup();
        $gitConfig = '[remote "origin"] url = https://github.com/username/repoOrigin.git';
        $setup->env->package()->addFile('.git/config', $gitConfig);
        $setup->addComposer([
            'package.name'     => 'fooBar/baz',
            'description.text' => 'My library package',
            'namespace.src'    => 'FooBarNamespace\\Baz'
        ]);

        $factory = new Initialize($setup->env, ['i' => false]);
        $factory->command()->execute();

        $this->assertGeneratedFiles($setup, [
            'repository.name'  => 'username/repoOrigin',
            'package.name'     => 'fooBar/baz',
            'description.text' => 'My library package',
            'namespace.src'    => 'FooBarNamespace\\Baz'
        ]);
    }

    public function testPackageNameValue_IsUsedAsFallbackForDefaultValues()
    {
        $setup = new EnvSetup();
        $setup->addComposer([
            'package.name'     => 'fooBar/baz',
            'description.text' => null,
            'namespace.src'    => null
        ]);

        $factory = new Initialize($setup->env, ['i' => false]);
        $factory->command()->execute();

        $this->assertGeneratedFiles($setup, [
            'repository.name'  => 'fooBar/baz',
            'package.name'     => 'fooBar/baz',
            'description.text' => 'fooBar/baz package',
            'namespace.src'    => 'FooBar\\Baz'
        ]);
    }

    public function testUnresolvedPackageNameValue_IsReadFromDirectoryStructure()
    {
        $setup = new EnvSetup();
        $setup->env->package()->path = '/path/foo/bar';

        $factory = new Initialize($setup->env, ['i' => false]);
        $factory->command()->execute();

        $this->assertGeneratedFiles($setup, [
            'repository.name'  => 'foo/bar',
            'package.name'     => 'foo/bar',
            'description.text' => 'foo/bar package',
            'namespace.src'    => 'Foo\\Bar'
        ]);
    }

    public function testCommandLineValues_OverwriteDefault()
    {
        $setup   = new EnvSetup();
        $options = [
            'repo'    => 'cli/repo',
            'package' => 'cli/package',
            'desc'    => 'cli desc',
            'ns'      => 'Cli\NamespaceX'
        ];

        $factory = new Initialize($setup->env, $options);
        $factory->command()->execute();

        $this->assertGeneratedFiles($setup, [
            'repository.name'  => 'cli/repo',
            'package.name'     => 'cli/package',
            'description.text' => 'cli desc',
            'namespace.src'    => 'Cli\NamespaceX'
        ]);
    }

    public function testInteractiveInputValues_OverwriteOtherSources()
    {
        $setup = new EnvSetup();
        $setup->env->input()->inputStrings = [
            'package/name',
            'user/repo',
            'package input description',
            'My\Namespace'
        ];
        $options = [
            'i'       => true,
            'repo'    => 'cli/repo',
            'package' => 'cli/package',
            'desc'    => 'cli desc',
            'ns'      => 'Cli\NamespaceX'
        ];

        $factory = new Initialize($setup->env, $options);
        $factory->command()->execute();

        $this->assertGeneratedFiles($setup, [
            'repository.name'  => 'user/repo',
            'package.name'     => 'package/name',
            'description.text' => 'package input description',
            'namespace.src'    => 'My\Namespace'
        ]);
    }

    public function testOverwrittenPackageFiles_AreCopiedIntoBackupDirectory()
    {
        $setup = new EnvSetup();
        $setup->env->skeleton()->addFile('file.ini', 'generated');
        $setup->env->package()->addFile('file.ini', 'original');

        $this->assertSame([], $setup->env->backup()->files());

        $factory = new Initialize($setup->env, []);
        $factory->command()->execute();

        $this->assertSame([$setup->env->backup()->file('file.ini')], $setup->env->backup()->files());
        $this->assertSame('original', $setup->env->backup()->file('file.ini')->contents());
        $this->assertSame('generated', $setup->env->package()->file('file.ini')->contents());
    }

    public function testExistingMetaDataFile_AbortsExecutionWithoutSideEffects()
    {
        $setup = new EnvSetup();
        $setup->env->skeleton()->addFile('file.ini', 'contents');
        $setup->addMetaData();
        $metaData = $setup->env->metaDataFile()->contents();

        $factory = new Initialize($setup->env, []);
        $factory->command()->execute();

        $this->assertSame($metaData, $setup->env->metaDataFile()->contents());
        $this->assertFalse($setup->env->package()->file('file.ini')->exists());
    }

    public function testOverwritingBackupFile_AbortsExecutionWithoutSideEffects()
    {
        $setup = new EnvSetup();
        $setup->env->skeleton()->addFile('file.ini', 'skeleton');
        $setup->env->package()->addFile('file.ini', 'original');
        $setup->env->backup()->addFile('file.ini', 'backup');

        $factory = new Initialize($setup->env, []);
        $factory->command()->execute();

        $this->assertSame('original', $setup->env->package()->file('file.ini')->contents());
        $this->assertSame('backup', $setup->env->backup()->file('file.ini')->contents());
        $this->assertFalse($setup->env->metaDataFile()->exists());
        $this->assertFalse($setup->env->package()->file('composer.json')->exists());
    }

    private function assertGeneratedFiles(EnvSetup $setup, array $data): void
    {
        $generatedFile = $setup->env->package()->file($setup::SKELETON_FILE)->contents();
        $this->assertSame($setup->render($data, false), $generatedFile);

        $expectedMetaData = json_encode($setup->metaData($data), JSON_PRETTY_PRINT);
        $this->assertSame($expectedMetaData, $setup->env->metaDataFile()->contents());
    }
}
