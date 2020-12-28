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
use Shudd3r\PackageFiles\InitCommandFactory as Factory;
use Shudd3r\PackageFiles\Environment\Command;
use Shudd3r\PackageFiles\Application\Token\Reader;


class InitCommandFactoryTest extends TestCase
{
    private const SKELETON_FILE = 'dir/generate.ini';

    public function testFactoryCreatesCommand()
    {
        $factory = new Factory($this->env(), []);
        $this->assertInstanceOf(Command::class, $factory->command());
    }

    public function testPropertiesAreReadFromProjectFiles()
    {
        $env     = $this->env();
        $factory = new Factory($env, ['i' => false]);
        $composer = [
            'name'        => 'fooBar/baz',
            'description' => 'My library package',
            'autoload'    => ['psr-4' => ['FooBarNamespace\\Baz\\' => 'src/']]
        ];
        $env->package()->addFile('composer.json', json_encode($composer));
        $iniData = '[remote "origin"] url = https://github.com/username/repositoryOrigin.git';
        $env->package()->addFile('.git/config', $iniData);

        $factory->command()->execute();

        $this->assertGeneratedFiles($env, [
            'repository.name'  => 'username/repositoryOrigin',
            'package.name'     => 'fooBar/baz',
            'description.text' => 'My library package',
            'namespace.src'    => 'FooBarNamespace\\Baz'
        ]);
    }

    public function testUnresolvedPropertiesAreReadFromDirectoryNames()
    {
        $env     = $this->env();
        $factory = new Factory($env, ['i' => false]);
        $env->package()->path = '/path/foo/bar';

        $factory->command()->execute();

        $this->assertGeneratedFiles($env, [
            'repository.name'  => 'foo/bar',
            'package.name'     => 'foo/bar',
            'description.text' => 'foo/bar package',
            'namespace.src'    => 'Foo\\Bar'
        ]);
    }

    public function testEstablishedPackageNameWillDetermineOtherProperties()
    {
        $env      = $this->env();
        $factory  = new Factory($env, ['i' => false]);
        $composer = json_encode(['name' => 'fooBar/baz']);
        $env->package()->addFile('composer.json', $composer);

        $factory->command()->execute();

        $this->assertGeneratedFiles($env, [
            'repository.name'  => 'fooBar/baz',
            'package.name'     => 'fooBar/baz',
            'description.text' => 'fooBar/baz package',
            'namespace.src'    => 'FooBar\\Baz'
        ]);
    }

    public function testCommandLineDefinedPropertiesHavePriorityOverResolved()
    {
        $env     = $this->env();
        $factory = new Factory($env, []);
        $factory->command()->execute();
        $this->assertGeneratedFiles($env, [
            'repository.name'  => 'package/directory',
            'package.name'     => 'package/directory',
            'description.text' => 'package/directory package',
            'namespace.src'    => 'Package\Directory'
        ]);

        $options = [
            'i'       => true,
            'repo'    => 'cli/repo',
            'package' => 'cli/package',
            'desc'    => 'cli desc',
            'ns'      => 'Cli\NamespaceX'
        ];

        $env     = $this->env();
        $factory = new Factory($env, $options);
        $factory->command()->execute();
        $this->assertGeneratedFiles($env, [
            'repository.name'  => 'cli/repo',
            'package.name'     => 'cli/package',
            'description.text' => 'cli desc',
            'namespace.src'    => 'Cli\NamespaceX'
        ]);
    }

    public function testInteractiveInputStringsWillOverwriteAllProperties()
    {
        $env = $this->env();

        $env->input()->inputStrings = [
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

        $factory = new Factory($env, $options);
        $factory->command()->execute();

        $this->assertGeneratedFiles($env, [
            'repository.name'  => 'user/repo',
            'package.name'     => 'package/name',
            'description.text' => 'package input description',
            'namespace.src'    => 'My\Namespace'
        ]);
    }

    public function testOverwrittenPackageFilesAreCopiedIntoBackupDirectory()
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->skeleton()->addFile('file.ini', 'generated');
        $env->package()->addFile('file.ini', 'original');
        $factory = new Factory($env, []);

        $this->assertSame([], $env->backup()->files());

        $factory->command()->execute();
        $this->assertSame([$env->backup()->file('file.ini')], $env->backup()->files());
        $this->assertSame('original', $env->backup()->file('file.ini')->contents());
        $this->assertSame('generated', $env->package()->file('file.ini')->contents());
    }

    public function testExistingMetaDataFile_AbortsExecutionWithoutSideEffects()
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->metaDataFile()->write('foo');
        $env->skeleton()->addFile('file.ini');
        $factory = new Factory($env, []);
        $command = $factory->command();

        $command->execute();
        $this->assertSame('foo', $env->metaDataFile()->contents());
        $this->assertFalse($env->package()->file('file.ini')->exists());
    }

    public function testOverwritingBackupFile_AbortsExecutionWithoutSideEffects()
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->skeleton()->addFile('file.ini', 'skeleton');
        $env->package()->addFile('file.ini', 'original');
        $env->backup()->addFile('file.ini', 'backup');
        $factory = new Factory($env, []);
        $command = $factory->command();

        $command->execute();
        $this->assertSame('original', $env->package()->file('file.ini')->contents());
        $this->assertSame('backup', $env->backup()->file('file.ini')->contents());
        $this->assertFalse($env->metaDataFile()->exists());
        $this->assertFalse($env->package()->file('composer.json')->exists());
    }

    private function assertGeneratedFiles(Doubles\FakeRuntimeEnv $env, array $data): void
    {
        $generatedFile = $env->package()->file(self::SKELETON_FILE)->contents();
        $this->assertSame($this->template($data), $generatedFile);

        $expectedMetaData = [
            Reader\PackageName::class        => $data['package.name'],
            Reader\RepositoryName::class     => $data['repository.name'],
            Reader\PackageDescription::class => $data['description.text'],
            Reader\SrcNamespace::class       => $data['namespace.src']
        ];

        $this->assertSame(json_encode($expectedMetaData, JSON_PRETTY_PRINT), $env->metaDataFile()->contents());
    }

    private function env(): Doubles\FakeRuntimeEnv
    {
        $env = new Doubles\FakeRuntimeEnv();

        $env->package()->path  = '/path/to/package/directory';
        $env->skeleton()->path = '/path/to/skeleton/files';

        $env->skeleton()->addFile(self::SKELETON_FILE, $this->template());

        return $env;
    }

    private function template(array $replacements = []): string
    {
        $skeleton = <<<'TPL'
            This is a template for {repository.name} in a {package.name} package, which
            is "{description.text}" with `src` directory files in `{namespace.src}` namespace.
            TPL;

        foreach ($replacements as $name => $replacement) {
            $skeleton = str_replace('{' . $name . '}', $replacement, $skeleton);
        }

        return $skeleton;
    }
}
