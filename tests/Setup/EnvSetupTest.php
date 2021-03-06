<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Setup;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Setup\EnvSetup;
use Shudd3r\Skeletons\Environment\Files\Directory;
use Shudd3r\Skeletons\Replacements\Data;
use Shudd3r\Skeletons\Exception;
use Shudd3r\Skeletons\Tests\Doubles;


class EnvSetupTest extends TestCase
{
    public function testRuntimeEnvMethod_CreatesRuntimeEnvWithGivenValues()
    {
        $package  = new Directory\VirtualDirectory();
        $skeleton = new Directory\VirtualDirectory();
        $terminal = new Doubles\MockedTerminal();
        $backup   = new Directory\VirtualDirectory();
        $setup    = new EnvSetup($package, $skeleton);

        $package->addFile($filename = '.dev/meta-data.json', '{}');
        $setup->setMetaFile($filename);
        $setup->setBackupDirectory($backup);

        $env = $setup->runtimeEnv($terminal);
        $this->assertSame($package, $env->package());
        $this->assertSame($skeleton, $env->skeleton());
        $this->assertSame($terminal, $env->output());
        $this->assertSame($terminal, $env->input());
        $this->assertSame($backup, $env->backup());
        $this->assertSame($package->file($filename), $env->metaDataFile());
        $this->assertEquals(new Data\ComposerJsonData($package->file('composer.json')), $env->composer());
        $this->assertEquals(new Data\MetaData($package->file($filename)), $env->metaData());
    }

    public function testResolvingDefaultValues()
    {
        $package = new Directory\VirtualDirectory();
        $setup   = new EnvSetup($package, new Directory\VirtualDirectory());

        $package->addFile($filename = '.github/skeleton.json', '{}');

        $env = $setup->runtimeEnv(new Doubles\MockedTerminal());
        $this->assertSame($package->subdirectory('.skeleton-backup'), $env->backup());
        $this->assertSame($package->file($filename), $env->metaDataFile());
    }

    public function testNotExistingPackageDirectory_RuntimeEnvMethod_ThrowsException()
    {
        $package = new Directory\VirtualDirectory('/some/path', false);
        $setup   = new EnvSetup($package, new Directory\VirtualDirectory());

        $this->expectException(Exception\InvalidDirectoryException::class);
        $setup->runtimeEnv(new Doubles\MockedTerminal());
    }

    public function testNotExistingSkeletonDirectory_RuntimeEnvMethod_ThrowsException()
    {
        $skeleton = new Directory\VirtualDirectory('/some/path', false);
        $setup    = new EnvSetup(new Directory\VirtualDirectory(), $skeleton);

        $this->expectException(Exception\InvalidDirectoryException::class);
        $setup->runtimeEnv(new Doubles\MockedTerminal());
    }
}
