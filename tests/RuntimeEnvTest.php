<?php

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
use Shudd3r\PackageFiles\Application\Terminal;
use Shudd3r\PackageFiles\Files\Exception;


class RuntimeEnvTest extends TestCase
{
    public function testInstantiation()
    {
        $this->assertInstanceOf(RuntimeEnv::class, $this->env());
    }

    public function testMethods_ReturnCorrespondingValues()
    {
        $params = [
            'input'       => $this->terminal(),
            'output'      => $this->terminal(),
            'packageDir'  => new Doubles\FakeDirectory(),
            'templateDir' => new Doubles\FakeDirectory()
        ];

        $env = $this->env($params);

        $this->assertSame($params['input'], $env->input());
        $this->assertSame($params['output'], $env->output());
        $this->assertSame($params['packageDir'], $env->packageFiles());
        $this->assertSame($params['templateDir'], $env->skeletonFiles());
    }

    public function testInstantiatingWithInvalidPackageDirectory_ThrowsException()
    {
        $this->expectException(Exception\InvalidDirectoryException::class);
        $this->env(['packageDir' => new Doubles\FakeDirectory(false)]);
    }

    public function testInstantiatingWithInvalidTemplateDirectory_ThrowsException()
    {
        $this->expectException(Exception\InvalidDirectoryException::class);
        $this->env(['templateDir' => new Doubles\FakeDirectory(false)]);
    }

    private function env(array $params = []): RuntimeEnv
    {
        return new RuntimeEnv(
            $params['input'] ?? $this->terminal(),
            $params['output'] ?? $this->terminal(),
            $params['packageDir'] ?? new Doubles\FakeDirectory(),
            $params['templateDir'] ?? new Doubles\FakeDirectory()
        );
    }

    private function terminal(): Terminal
    {
        $stream = fopen('php://memory', 'r+');
        return new Terminal($stream, $stream, $stream);
    }
}
