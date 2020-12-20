<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Command\Factory;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Command\Factory\InitCommandFactory as Factory;
use Shudd3r\PackageFiles\Application\Command;
use Shudd3r\PackageFiles\Token\Reader;
use Shudd3r\PackageFiles\Tests\Doubles;
use Exception;


class InitCommandFactoryTest extends TestCase
{
    private const SKELETON_FILE = 'dir/generate.ini';
    private const METADATA_FILE = '.github/skeleton.json';

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
        $env = $this->env();

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

    public function testOverwritingBackupFile_ThrowsException()
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->skeleton()->addFile('file.ini');
        $env->package()->addFile('file.ini');
        $env->backup()->addFile('file.ini');
        $factory = new Factory($env, []);

        $this->expectException(Exception::class);
        $factory->command()->execute();
    }

    public function testOverwritingBackupFile_AbortsExecutionWithoutSideEffects()
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->skeleton()->addFile('a.file', 'generated');
        $env->skeleton()->addFile('b.file', 'generated');
        $env->skeleton()->addFile('c.file', 'generated');
        $env->skeleton()->addFile('d.file', 'generated');
        $env->package()->addFile('b.file', 'original');
        $env->package()->addFile('c.file', 'original');
        $env->package()->addFile('d.file', 'original');
        $env->backup()->addFile('c.file', 'backup');

        $factory = new Factory($env, []);

        try {
            $factory->command()->execute();
        } catch (Exception $e) {
            $this->assertFalse($env->backup()->file('a.file')->exists());
            $this->assertFalse($env->backup()->file('b.file')->exists());
            $this->assertSame('backup', $env->backup()->file('c.file')->contents());
            $this->assertFalse($env->backup()->file('d.file')->exists());

            $this->assertFalse($env->package()->file('a.file')->exists());
            $this->assertSame('original', $env->package()->file('b.file')->contents());
            $this->assertSame('original', $env->package()->file('c.file')->contents());
            $this->assertSame('original', $env->package()->file('d.file')->contents());

            $this->assertFalse($env->package()->file('composer.json')->exists());
        }
    }

    private function assertGeneratedFiles(Doubles\FakeRuntimeEnv $env, array $data): void
    {
        $generatedFile = $env->package()->file(self::SKELETON_FILE)->contents();
        $this->assertSame($this->template($data), $generatedFile);

        $metaDataFile = $env->package()->file(self::METADATA_FILE)->contents();

        $expectedMetaData = [
            Reader\PackageName::class        => $data['package.name'],
            Reader\RepositoryName::class     => $data['repository.name'],
            Reader\PackageDescription::class => $data['description.text'],
            Reader\SrcNamespace::class       => $data['namespace.src']
        ];

        $this->assertSame(json_encode($expectedMetaData, JSON_PRETTY_PRINT), $metaDataFile);
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
