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
use Shudd3r\PackageFiles\Initialize;
use Shudd3r\PackageFiles\Environment\Command;
use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Replacement;
use Shudd3r\PackageFiles\Tests\Doubles;


class InitializeTest extends TestCase
{
    public const PACKAGE_NAME  = 'package.name';
    public const PACKAGE_DESC  = 'package.description';
    public const SRC_NAMESPACE = 'namespace.src';
    public const REPO_NAME     = 'repository.name';

    public function testInitialization_GeneratesFilesFromTemplate()
    {
        $files      = new Fixtures\ExampleFiles('example-files');
        $package    = $files->directory('package');
        $initialize = $this->initialize($package, $files->directory('template'));

        $initialize->execute();

        $expectedFiles = new Fixtures\ExampleFiles('example-files/package-initialized');
        $this->assertTrue($expectedFiles->hasSameFilesAs($package));
    }

    public function testExistingMetaDataFile_AbortsExecutionWithoutSideEffects()
    {
        $files      = new Fixtures\ExampleFiles('example-files');
        $package    = $files->directory('package');
        $initialize = $this->initialize($package, $files->directory('template'), null, new Doubles\MockedFile());

        $initialize->execute();

        $expectedFiles = new Fixtures\ExampleFiles('example-files/package');
        $this->assertTrue($expectedFiles->hasSameFilesAs($package));
    }

    public function testOverwritingBackupFile_AbortsExecutionWithoutSideEffects()
    {
        $files   = new Fixtures\ExampleFiles('example-files');
        $package = $files->directory('package');
        $backup  = new Doubles\FakeDirectory();
        $backup->addFile('README.md', 'anything');
        $initialize = $this->initialize($package, $files->directory('template'), $backup);

        $initialize->execute();

        $expectedFiles = new Fixtures\ExampleFiles('example-files/package');
        $this->assertTrue($expectedFiles->hasSameFilesAs($package));
    }

    private function initialize(
        Directory $package,
        Directory $skeleton,
        ?Directory $backup = null,
        File $metaFile = null
    ): Command {
        $terminal = new Doubles\MockedTerminal();
        $env = new RuntimeEnv($terminal, $terminal, $package, $skeleton, $backup, $metaFile);

        $replacements = $env->replacements();
        $replacements->add(self::PACKAGE_NAME, $packageName = new Replacement\PackageName($env));
        $replacements->add(self::REPO_NAME, new Replacement\RepositoryName($env, $packageName));
        $replacements->add(self::PACKAGE_DESC, new Replacement\PackageDescription($env, $packageName));
        $replacements->add(self::SRC_NAMESPACE, new Replacement\SrcNamespace($env, $packageName));

        $jsonMerge = fn (Template $template, File $composer) => new Template\MergedJsonTemplate($template, $composer);
        $env->addTemplate('composer.json', $jsonMerge);

        $initialize = new Initialize($env);
        $options    = [
            'repo'    => 'initial/repo',
            'package' => 'initial/package-name',
            'desc'    => 'Initial package description',
            'ns'      => 'Package\Initial'
        ];

        return $initialize->command($options);
    }
}
