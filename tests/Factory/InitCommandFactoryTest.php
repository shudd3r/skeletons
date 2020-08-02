<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Factory\InitCommandFactory as Factory;
use Shudd3r\PackageFiles\Application\Command;
use Shudd3r\PackageFiles\Tests\Doubles;


class InitCommandFactoryTest extends TestCase
{
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
        $env->packageFiles()->files['composer.json'] = new Doubles\MockedFile(json_encode($composer));
        $iniData = '[remote "origin"] url = https://github.com/username/repositoryOrigin.git';
        $env->packageFiles()->files['.git/config'] = new Doubles\MockedFile($iniData);

        $factory->command()->execute();

        $this->assertMetaDataFile($env, [
            'original_repository' => 'username/repositoryOrigin',
            'package_name'        => 'fooBar/baz',
            'package_desc'        => 'My library package',
            'source_namespace'    => 'FooBarNamespace\\Baz'
        ]);
    }

    public function testUnresolvedPropertiesAreReadFromDirectoryNames()
    {
        $env     = $this->env();
        $factory = new Factory($env, ['i' => false]);
        $env->packageFiles()->path = '/path/foo/bar';

        $factory->command()->execute();

        $this->assertMetaDataFile($env, [
            'original_repository' => 'foo/bar',
            'package_name'        => 'foo/bar',
            'package_desc'        => 'foo/bar package',
            'source_namespace'    => 'Foo\\Bar'
        ]);
    }

    public function testEstablishedPackageNameWillDetermineOtherProperties()
    {
        $env      = $this->env();
        $factory  = new Factory($env, ['i' => false]);
        $composer = json_encode(['name' => 'fooBar/baz']);
        $env->packageFiles()->files['composer.json'] = new Doubles\MockedFile($composer);

        $factory->command()->execute();

        $this->assertMetaDataFile($env, [
            'original_repository' => 'fooBar/baz',
            'package_name'        => 'fooBar/baz',
            'package_desc'        => 'fooBar/baz package',
            'source_namespace'    => 'FooBar\\Baz'
        ]);
    }

    public function testCommandLineDefinedPropertiesHavePriorityOverResolved()
    {
        $env = $this->env();

        $factory = new Factory($env, []);
        $factory->command()->execute();
        $this->assertMetaDataFile($env, [
            'original_repository' => 'package/directory',
            'package_name'        => 'package/directory',
            'package_desc'        => 'package/directory package',
            'source_namespace'    => 'Package\Directory'
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
        $this->assertMetaDataFile($env, [
            'original_repository' => 'cli/repo',
            'package_name'        => 'cli/package',
            'package_desc'        => 'cli desc',
            'source_namespace'    => 'Cli\NamespaceX'
        ]);
    }

    public function testInteractiveInputStringsWillOverwriteAllProperties()
    {
        $env = $this->env();

        $env->input()->inputStrings = [
            'user/repo',
            'package/name',
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

        $this->assertMetaDataFile($env, [
            'original_repository' => 'user/repo',
            'package_name'        => 'package/name',
            'package_desc'        => 'package input description',
            'source_namespace'    => 'My\Namespace'
        ]);
    }

    private function assertMetaDataFile(Doubles\FakeRuntimeEnv $env, array $data): void
    {
        $metaDataFile = $env->packageFiles()->files['.github/package.properties']->contents();
        $this->assertSame($data, parse_ini_string($metaDataFile));
    }

    private function env(): Doubles\FakeRuntimeEnv
    {
        $env = new Doubles\FakeRuntimeEnv();

        $env->directory->path = '/path/to/package/directory';
        $env->templates->path = '/path/to/skeleton/files';
        $env->templates->files['package.properties'] = new Doubles\MockedFile(
            <<<'TPL'
            original_repository={repository.name}
            package_name={package.name}
            package_desc={description.text}
            source_namespace={namespace.src}
            TPL
        );

        return $env;
    }
}
