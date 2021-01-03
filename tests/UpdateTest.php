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
use Shudd3r\PackageFiles\Update;
use Shudd3r\PackageFiles\Environment\Command;
use Shudd3r\PackageFiles\Application\Token\Reader;
use Shudd3r\PackageFiles\Tests\Doubles;


class UpdateTest extends TestCase
{
    private const SKELETON_FILE = 'dir/generate.ini';

    public function testFactory_ReturnsCommand()
    {
        $factory = new Update($this->env(), []);
        $this->assertInstanceOf(Command::class, $factory->command());
    }

    public function testValuesProvidedAsCommandLineOptions_UpdatePackage()
    {
        $env = $this->env();
        $old = $this->defaultData();
        $this->createMetaData($env, $old);
        $env->package()->addFile('composer.json', $this->composer($old));
        $env->package()->addFile(self::SKELETON_FILE, $this->template($old));

        $factory = new Update($env, ['ns' => 'New\\Namespace', 'repo' => 'new/repo']);
        $factory->command()->execute();

        $new = [
            'repository.name' => 'new/repo',
            'namespace.src'   => 'New\\Namespace'
        ] + $old;

        $this->assertSame($this->template($new), $env->package()->file(self::SKELETON_FILE)->contents());
        $this->assertSame($this->composer($new), $env->package()->file('composer.json')->contents());
    }

    public function testValuesProvidedAsInteractiveInput_UpdatePackage()
    {
        $env = $this->env();
        $old = $this->defaultData();
        $this->createMetaData($env, $old);
        $env->package()->addFile('composer.json', $this->composer($old));
        $env->package()->addFile(self::SKELETON_FILE, $this->template($old));
        $env->input()->inputStrings = ['new/package', '', '!!! This is new description !!!', ''];

        $factory = new Update($env, ['repo' => 'new/repo', 'i' => true]);
        $factory->command()->execute();

        $new = [
            'package.name'     => 'new/package',
            'repository.name'  => 'new/repo',
            'description.text' => '!!! This is new description !!!'
        ] + $old;

        $this->assertSame($this->template($new), $env->package()->file(self::SKELETON_FILE)->contents());
        $this->assertSame($this->composer($new), $env->package()->file('composer.json')->contents());
    }

    public function testMissingMetaDataFile_PreventsExecution()
    {
        $env = $this->env();
        $old = $this->defaultData();
        $env->package()->addFile(self::SKELETON_FILE, $oldContents = $this->template($old));
        $env->package()->addFile('composer.json', $this->composer($old));

        $factory = new Update($env, ['repo' => 'new/repo']);
        $factory->command()->execute();

        $this->assertSame($oldContents, $env->package()->file(self::SKELETON_FILE)->contents());
    }

    public function testNotSynchronizedMetaData_PreventsExecution()
    {
        $env = $this->env();
        $old = $this->defaultData();
        $this->createMetaData($env, $old);
        $contents = $this->template(['repository.name' => 'other/repo'] + $old);
        $env->package()->addFile(self::SKELETON_FILE, $contents);
        $env->package()->addFile('composer.json', $this->composer($old));

        $factory = new Update($env, ['repo' => 'new/repo']);
        $factory->command()->execute();

        $this->assertSame($contents, $env->package()->file(self::SKELETON_FILE)->contents());
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
        $orig = $replacements ? [
            ' (and this is some original content not present in template file)',
            '--- this was extracted from package file ---'
        ] : [
            '{original.content}', '{original.content}'
        ];

        $skeleton = <<<TPL
            This is a template for {repository.name} in a {package.name} package{$orig[0]}, which
            is "{description.text}" with `src` directory files in `{namespace.src}` namespace.
            
            {$orig[1]}
            TPL;

        foreach ($replacements as $name => $replacement) {
            $skeleton = str_replace('{' . $name . '}', $replacement, $skeleton);
        }

        return $skeleton;
    }

    private function createMetaData(Doubles\FakeRuntimeEnv $env, array $data = [])
    {
        $data += $this->defaultData();

        $metaData = [
            Reader\PackageName::class        => $data['package.name'],
            Reader\RepositoryName::class     => $data['repository.name'],
            Reader\PackageDescription::class => $data['description.text'],
            Reader\SrcNamespace::class       => $data['namespace.src']
        ];

        $env->metaDataFile()->write(json_encode($metaData, JSON_PRETTY_PRINT));
    }

    private function defaultData(): array
    {
        return [
            'package.name'     => 'default/package',
            'repository.name'  => 'default/repo',
            'description.text' => 'This is default description',
            'namespace.src'    => 'Default\\Namespace'
        ];
    }

    private function composer(array $data = []): string
    {
        $ns = str_replace('//', '////', $data['namespace.src'] ?? 'Default\Namespace') . '\\';

        $composer = [
            'name'              => $data['package.name'] ?? 'default/name',
            'description'       => $data['description.text'] ?? 'default description',
            'type'              => 'library',
            'license'           => 'MIT',
            'authors'           => [['name' => 'Shudd3r', 'email' => 'q3.shudder@gmail.com']],
            'autoload'          => ['psr-4' => [$ns => 'src/']],
            'autoload-dev'      => ['psr-4' => [$ns . 'Tests\\' => 'tests/']],
            'minimum-stability' => 'stable'
        ];

        return json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    }
}
