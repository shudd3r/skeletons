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
use Shudd3r\PackageFiles\RuntimeEnv;
use Shudd3r\PackageFiles\Exception;


class RuntimeEnvTest extends TestCase
{
    public function testInstantiation()
    {
        $this->assertInstanceOf(RuntimeEnv::class, $this->env());
    }

    public function testMethods_ReturnCorrespondingValues()
    {
        $env = $this->env($params);

        $this->assertSame($params['input'], $env->input());
        $this->assertSame($params['output'], $env->output());
        $this->assertSame($params['packageDir'], $env->package());
        $this->assertSame($params['templateDir'], $env->skeleton());
        $this->assertSame($params['backupDir'], $env->backup());
        $this->assertSame($params['metaData'], $env->metaDataFile());
    }

    public function testDefaultBackupFile()
    {
        $env = new RuntimeEnv(
            new Doubles\MockedTerminal(),
            new Doubles\MockedTerminal(),
            $packageDirectory = new Doubles\FakeDirectory(),
            new Doubles\FakeDirectory()
        );

        $this->assertSame($env->backup(), $packageDirectory->subdirectory('.skeleton-backup'));
    }

    public function testDefaultMetaDataFile()
    {
        $packageDirectory = new Doubles\FakeDirectory();
        $packageDirectory->addFile('.github/skeleton.json');

        $env = new RuntimeEnv(
            new Doubles\MockedTerminal(),
            new Doubles\MockedTerminal(),
            $packageDirectory,
            new Doubles\FakeDirectory()
        );

        $this->assertSame($env->metaDataFile(), $packageDirectory->file('.github/skeleton.json'));
    }

    public function testInstantiatingWithInvalidPackageDirectory_ThrowsException()
    {
        $params = ['packageDir' => new Doubles\FakeDirectory('/package/dir', false)];

        $this->expectException(Exception\InvalidDirectoryException::class);
        $this->env($params);
    }

    public function testInstantiatingWithInvalidTemplateDirectory_ThrowsException()
    {
        $params = ['templateDir' => new Doubles\FakeDirectory('/template/dir', false)];

        $this->expectException(Exception\InvalidDirectoryException::class);
        $this->env($params);
    }

    private function env(?array &$params = []): RuntimeEnv
    {
        return new RuntimeEnv(
            $params['input'] ??= new Doubles\MockedTerminal(),
            $params['output'] ??= new Doubles\MockedTerminal(),
            $params['packageDir'] ??= new Doubles\FakeDirectory(),
            $params['templateDir'] ??= new Doubles\FakeDirectory(),
            $params['backupDir'] ??= new Doubles\FakeDirectory(),
            $params['metaData'] ??= new Doubles\MockedFile()
        );
    }
}
