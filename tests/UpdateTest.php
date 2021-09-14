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
use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Replacement;
use Shudd3r\PackageFiles\Tests\Doubles;


class UpdateTest extends TestCase
{
    public const PACKAGE_NAME  = 'package.name';
    public const PACKAGE_DESC  = 'package.description';
    public const SRC_NAMESPACE = 'namespace.src';
    public const REPO_NAME     = 'repository.name';

    public function testSynchronizedPackage_IsUpdated()
    {
        $files   = new Fixtures\ExampleFiles('example-files');
        $package = $files->directory('package-synchronized');
        $env     = $this->envSetup($package, $files->directory('template'));

        $this->updateCommand($env)->execute();

        $expectedFiles = new Fixtures\ExampleFiles('example-files/package-updated');
        $this->assertTrue($expectedFiles->hasSameFilesAs($package));
    }

    public function testPackageWithoutMetaDataFile_IsNotUpdated()
    {
        $files   = new Fixtures\ExampleFiles('example-files');
        $package = $files->directory('package-synchronized');
        $env     = $this->envSetup($package, $files->directory('template'), new Doubles\MockedFile(null));

        $this->updateCommand($env)->execute();

        $expectedFiles = new Fixtures\ExampleFiles('example-files/package-synchronized');
        $this->assertTrue($expectedFiles->hasSameFilesAs($package));
    }

    public function testDesynchronizedPackage_IsNotUpdated()
    {
        $files   = new Fixtures\ExampleFiles('example-files');
        $package = $files->directory('package-unsynchronized');
        $env     = $this->envSetup($package, $files->directory('template'), new Doubles\MockedFile(null));

        $this->updateCommand($env)->execute();

        $expectedFiles = new Fixtures\ExampleFiles('example-files/package-unsynchronized');
        $this->assertTrue($expectedFiles->hasSameFilesAs($package));
    }

    private function envSetup(
        Directory $package,
        Directory $skeleton,
        File $metaFile = null
    ): RuntimeEnv {
        $terminal = new Doubles\MockedTerminal();
        $env = new RuntimeEnv($terminal, $terminal, $package, $skeleton, null, $metaFile);

        $replacements = $env->replacements();
        $replacements->add(self::PACKAGE_NAME, $packageName = new Replacement\PackageName($env));
        $replacements->add(self::REPO_NAME, new Replacement\RepositoryName($env, $packageName));
        $replacements->add(self::PACKAGE_DESC, new Replacement\PackageDescription($env, $packageName));
        $replacements->add(self::SRC_NAMESPACE, new Replacement\SrcNamespace($env, $packageName));

        $jsonMerge = fn (Template $template, File $composer) => new Template\MergedJsonTemplate($template, $composer);
        $env->addTemplate('composer.json', $jsonMerge);

        return $env;
    }

    private function updateCommand(RuntimeEnv $env): Command
    {
        $update = new Update($env);
        return $update->command([
            'repo'    => 'updated/repo',
            'package' => 'updated/package-name',
            'desc'    => 'Updated package description',
            'ns'      => 'Package\Updated'
        ]);
    }
}
