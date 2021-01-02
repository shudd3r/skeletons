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
use Shudd3r\PackageFiles\Validate;
use Shudd3r\PackageFiles\Application\Command\Factory;
use Shudd3r\PackageFiles\Tests\Doubles\FakeRuntimeEnv;
use Shudd3r\PackageFiles\Application\Token\Reader;


class ValidateTest extends TestCase
{
    private const SKELETON_FILE = 'dir/generate.ini';

    public function testInstantiation()
    {
        $this->assertInstanceOf(Factory::class, $this->factory());
    }

    public function testMissingMetaDataFile_StopsExecution()
    {
        $env     = $this->env();
        $factory = $this->factory($env);

        $factory->command()->execute();
        $this->assertSame([], $env->output()->messagesSent);
    }

    public function testInvalidMetaData_StopsExecution()
    {
        $env     = $this->env();
        $factory = $this->factory($env);

        $tokens = [
            'repository.name'  => 'user/repo',
            'package.name'     => 'package/name',
            'description.text' => 'package input description',
            'namespace.src'    => 'Not/A/Namespace'
        ];
        $this->createMetaData($env, $tokens);

        $factory->command()->execute();
        $this->assertSame([], $env->output()->messagesSent);
    }

    public function testMatchingFiles_RenderSuccessMessage()
    {
        $env     = $this->env();
        $factory = $this->factory($env);

        $tokens = [
            'repository.name'  => 'user/repo',
            'package.name'     => 'package/name',
            'description.text' => 'package input description',
            'namespace.src'    => 'My\Namespace'
        ];
        $this->createMetaData($env, $tokens);
        $env->package()->addFile(self::SKELETON_FILE, $this->template($tokens));
        $env->package()->addFile('composer.json', $this->composer($tokens));

        $factory->command()->execute();
        $this->assertSame(0, $env->output()->exitCode());
    }

    public function testNotMatchingFiles_RenderFailMessage()
    {
        $env     = $this->env();
        $factory = $this->factory($env);

        $tokens = [
            'repository.name'  => 'user/repo',
            'package.name'     => 'package/name',
            'description.text' => 'package input description',
            'namespace.src'    => 'My\Namespace'
        ];

        $this->createMetaData($env, ['repository.name' => 'another/repo'] + $tokens);
        $env->package()->addFile(self::SKELETON_FILE, $this->template($tokens));
        $env->package()->addFile('composer.json', $this->composer($tokens));

        $factory->command()->execute();
        $this->assertSame(1, $env->output()->exitCode());
    }

    private function factory(FakeRuntimeEnv &$env = null): Validate
    {
        return new Validate($env ??= $this->env(), []);
    }

    private function env(): Doubles\FakeRuntimeEnv
    {
        $env = new Doubles\FakeRuntimeEnv();

        $env->package()->path  = '/path/to/package/directory';
        $env->skeleton()->path = '/path/to/skeleton/files';

        $env->skeleton()->addFile(self::SKELETON_FILE, $this->template());
        return $env;
    }

    private function createMetaData(FakeRuntimeEnv $env, array $data = [])
    {
        $metaData = [
            Reader\PackageName::class        => $data['package.name'] ?? 'meta/package',
            Reader\RepositoryName::class     => $data['repository.name'] ?? 'meta/repo',
            Reader\PackageDescription::class => $data['description.text'] ?? 'This is meta package description',
            Reader\SrcNamespace::class       => $data['namespace.src'] ?? 'Meta\SrcNamespace'
        ];

        $env->metaDataFile()->write(json_encode($metaData, JSON_PRETTY_PRINT));
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
