<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Command;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Command;
use Shudd3r\PackageFiles\RuntimeEnv;
use Shudd3r\PackageFiles\Tests\Doubles;


class InitCommandTest extends TestCase
{
    private Doubles\FakeRuntimeEnv $env;

    public function testInstantiation()
    {
        $this->assertInstanceOf(Command::class, $this->factory()->command(['i' => false]));
        $this->assertInstanceOf(RuntimeEnv::class, $this->env);
    }

    public function testPropertiesAreReadFromProjectFiles()
    {
        $factory = $this->factory();
        $composer = json_encode([
            'name'        => 'fooBar/baz',
            'description' => 'My library package',
            'autoload'    => ['psr-4' => ['FooBarNamespace\\Baz\\' => 'src/']]
        ]);
        $this->env->packageFiles()->files['composer.json'] = new Doubles\MockedFile($composer);
        $iniData = '[remote "origin"] url = https://github.com/username/repositoryOrigin.git';
        $this->env->packageFiles()->files['.git/config'] = new Doubles\MockedFile($iniData);

        $factory->command(['i' => false])->execute();

        $this->assertMetaDataFile([
            'original_repository' => 'https://github.com/username/repositoryOrigin.git',
            'package_name'        => 'fooBar/baz',
            'package_desc'        => 'My library package',
            'source_namespace'    => 'FooBarNamespace\\Baz'
        ]);
    }

    public function testUnresolvedPropertiesAreReadFromDirectoryNames()
    {
        $command = $this->factory()->command(['i' => false]);
        $this->env->packageFiles()->path = '/path/foo/bar';

        $command->execute();

        $this->assertMetaDataFile([
            'original_repository' => 'https://github.com/foo/bar.git',
            'package_name'        => 'foo/bar',
            'package_desc'        => 'foo/bar package',
            'source_namespace'    => 'Foo\\Bar'
        ]);
    }

    public function testEstablishedPackageNameWillDetermineOtherProperties()
    {
        $factory  = $this->factory();
        $composer = json_encode(['name' => 'fooBar/baz']);
        $this->env->packageFiles()->files['composer.json'] = new Doubles\MockedFile($composer);

        $factory->command(['i' => false])->execute();

        $this->assertMetaDataFile([
            'original_repository' => 'https://github.com/fooBar/baz.git',
            'package_name'        => 'fooBar/baz',
            'package_desc'        => 'fooBar/baz package',
            'source_namespace'    => 'FooBar\\Baz'
        ]);
    }

    public function testCommandLineDefinedPropertiesHavePriorityOverResolved()
    {
        $factory = $this->factory();

        $factory->command([])->execute();
        $this->assertMetaDataFile([
            'original_repository' => 'https://github.com/package/directory.git',
            'package_name'        => 'package/directory',
            'package_desc'        => 'package/directory package',
            'source_namespace'    => 'Package\Directory'
        ]);

        $options = [
            'i'       => true,
            'repo'    => 'https://github.com/cli/repo.git',
            'package' => 'cli/package',
            'desc'    => 'cli desc',
            'ns'      => 'Cli\NamespaceX'
        ];

        $factory->command($options)->execute();
        $this->assertMetaDataFile([
            'original_repository' => 'https://github.com/cli/repo.git',
            'package_name'        => 'cli/package',
            'package_desc'        => 'cli desc',
            'source_namespace'    => 'Cli\NamespaceX'
        ]);
    }

    public function testInteractiveInputStringsWillOverwriteAllProperties()
    {
        $factory = $this->factory();
        $this->env->input()->inputStrings = [
            'https://github.com/user/repo.git',
            'package/name',
            'My\Namespace',
            'package input description'
        ];

        $options = [
            'i'       => true,
            'repo'    => 'https://github.com/cli/repo.git',
            'package' => 'cli/package',
            'desc'    => 'cli desc',
            'ns'      => 'Cli\NamespaceX'
        ];

        $factory->command($options)->execute();
        $this->assertMetaDataFile([
            'original_repository' => 'https://github.com/user/repo.git',
            'package_name'        => 'package/name',
            'package_desc'        => 'package input description',
            'source_namespace'    => 'My\Namespace'
        ]);
    }

    private function assertMetaDataFile(array $data): void
    {
        $metaDataFile = $this->env->packageFiles()->files['.github/package.properties']->contents();
        $this->assertSame($data, parse_ini_string($metaDataFile));
    }

    private function factory(): Command\Factory
    {
        $terminal = new Doubles\MockedTerminal();
        $package  = new Doubles\FakeDirectory(true, '/path/to/package/directory');
        $skeleton = new Doubles\FakeDirectory(true, '/path/to/skeleton/files');

        $skeleton->files['package.properties'] = new Doubles\MockedFile(
            <<<'TPL'
            original_repository={REPO_URL}
            package_name={PACKAGE_NAME}
            package_desc={PACKAGE_DESC}
            source_namespace={PACKAGE_NS}
            TPL
        );

        $this->env = new Doubles\FakeRuntimeEnv($terminal, $package, $skeleton);
        return new Command\Factory\InitCommandFactory($this->env);
    }
}
