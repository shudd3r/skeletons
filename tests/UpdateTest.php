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


class UpdateTest extends TestCase
{
    public function testFactory_ReturnsCommand()
    {
        $factory = new Update(new Doubles\FakeRuntimeEnv(), []);
        $this->assertInstanceOf(Command::class, $factory->command());
    }

    public function testValuesProvidedAsCommandLineOptions_UpdatePackage()
    {
        $setup = new EnvSetup();
        $data  = $setup->data();
        $setup->addMetaData();
        $setup->addGeneratedFile();
        $setup->addComposer();

        $factory = new Update($setup->env, ['ns' => 'New\\Namespace', 'repo' => 'new/repo']);
        $factory->command()->execute();

        $newData = $setup->data([
            'repository.name' => 'new/repo',
            'namespace.src'   => 'New\\Namespace'
        ]);

        $this->assertPackageFiles($setup, $newData, $data);
    }

    public function testValuesProvidedAsInteractiveInput_UpdatePackage()
    {
        $setup = new EnvSetup();
        $data  = $setup->data();
        $setup->addMetaData();
        $setup->addGeneratedFile();
        $setup->addComposer();
        $setup->env->input()->inputStrings = ['new/package', '', '!!! This is new description !!!', ''];

        $factory = new Update($setup->env, ['repo' => 'new/repo', 'i' => true]);
        $factory->command()->execute();

        $newData = $setup->data([
            'package.name'     => 'new/package',
            'repository.name'  => 'new/repo',
            'description.text' => '!!! This is new description !!!'
        ]);

        $this->assertPackageFiles($setup, $newData, $data);
    }

    public function testMissingMetaDataFile_PreventsExecution()
    {
        $setup = new EnvSetup();
        $data  = $setup->data();
        $setup->addGeneratedFile();
        $setup->addComposer();

        $factory = new Update($setup->env, ['repo' => 'new/repo']);
        $factory->command()->execute();

        $this->assertPackageFiles($setup, $data);
    }

    public function testNotSynchronizedMetaData_PreventsExecution()
    {
        $setup = new EnvSetup();
        $data  = $setup->data();
        $setup->addMetaData(['repository.name' => 'other/repo']);
        $setup->addGeneratedFile();
        $setup->addComposer();

        $factory = new Update($setup->env, ['repo' => 'new/repo']);
        $factory->command()->execute();

        $this->assertPackageFiles($setup, $data);
    }

    private function assertPackageFiles(EnvSetup $setup, array $data, array $oldData = null)
    {
        if ($oldData) { $this->assertNotEquals($oldData, $data); }
        $this->assertSame($setup->render($data), $setup->env->package()->file($setup::SKELETON_FILE)->contents());
        $this->assertSame($setup->composer($data), $setup->env->package()->file('composer.json')->contents());
    }
}
