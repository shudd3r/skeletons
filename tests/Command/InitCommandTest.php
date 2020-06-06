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

    private function factory(): Command\Factory
    {
        $terminal = new Doubles\MockedTerminal();
        $package  = new Doubles\FakeDirectory(true, '/path/to/package/directory');
        $skeleton = new Doubles\FakeDirectory(true, '/path/to/skeleton/files');

        $metaDataTemplate = <<<'TPL'
            original_repository={REPO_URL}
            package_name={PACKAGE_NAME}
            package_desc={PACKAGE_DESC}
            source_namespace={PACKAGE_NS}
            TPL;

        $skeleton->files = [
            'package.properties' => $metaDataTemplate
        ];

        $this->env = new Doubles\FakeRuntimeEnv($terminal, $package, $skeleton);
        return new Command\Factory\InitCommandFactory($this->env);
    }
}
