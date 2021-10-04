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
use Shudd3r\PackageFiles\Application;
use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Replacement;


class ApplicationTest extends TestCase
{
    private const PACKAGE_NAME  = 'package.name';
    private const PACKAGE_DESC  = 'package.description';
    private const SRC_NAMESPACE = 'namespace.src';
    private const REPO_NAME     = 'repository.name';

    private static Fixtures\ExampleFiles $files;
    private static Doubles\FakeDirectory $skeleton;

    private array $initOptions = [
        'repo'    => 'initial/repo',
        'package' => 'initial/package-name',
        'desc'    => 'Initial package description',
        'ns'      => 'Package\Initial'
    ];

    private array $updateOptions = [
        'repo'    => 'updated/repo',
        'package' => 'updated/package-name',
        'desc'    => 'Updated package description',
        'ns'      => 'Package\Updated'
    ];

    public static function setUpBeforeClass(): void
    {
        self::$files    = new Fixtures\ExampleFiles('example-files');
        self::$skeleton = self::$files->directory('template');
    }

    public function testUnknownCommand_ReturnsErrorCode()
    {
        $app = $this->app(self::$files->directory('package'));
        $this->assertNotEquals(0, $app->run('unknown', []));
    }

    public function testInitialization_GeneratesFilesFromTemplate()
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package);

        $this->assertSame(0, $app->run('init', $this->initOptions));
        $this->assertSameFiles($package, 'package-initialized');
    }

    public function testInitializationWithExistingMetaDataFile_AbortsExecutionWithoutSideEffects()
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package, true);

        $this->assertNotEquals(0, $app->run('init', $this->initOptions));
        $this->assertSameFiles($package, 'package');
    }

    public function testInitializationOverwritingBackupFile_AbortsExecutionWithoutSideEffects()
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package, null, true);

        $this->assertNotEquals(0, $app->run('init', $this->initOptions));
        $this->assertSameFiles($package, 'package');
    }

    public function testInitializedPackage_IsValid()
    {
        $app = $this->app(self::$files->directory('package-initialized'));
        $this->assertSame(0, $app->run('check'));
    }

    public function testSynchronizedPackage_IsValid()
    {
        $app = $this->app(self::$files->directory('package-synchronized'));
        $this->assertSame(0, $app->run('check'));
    }

    public function testDesynchronizedPackage_IsInvalid()
    {
        $app = $this->app(self::$files->directory('package-desynchronized'));
        $this->assertNotEquals(0, $app->run('check'));
    }

    public function testPackageWithoutMetaDataFile_IsInvalid()
    {
        $app = $this->app(self::$files->directory('package-synchronized'), false);
        $this->assertNotEquals(0, $app->run('check'));
    }

    public function testUpdatingSynchronizedPackage_GeneratesUpdatedPackageFiles()
    {
        $package = self::$files->directory('package-synchronized');
        $app     = $this->app($package);

        $this->assertSame(0, $app->run('update', $this->updateOptions));
        $this->assertSameFiles($package, 'package-updated');
    }

    public function testUpdatingPackageWithoutMetaDataFile_AbortsExecutionWithoutSideEffects()
    {
        $package = self::$files->directory('package-synchronized');
        $app     = $this->app($package, false);

        $this->assertNotEquals(0, $app->run('update', $this->updateOptions));
        $this->assertSameFiles($package, 'package-synchronized');
    }

    public function testUpdatingDesynchronizedPackage_AbortsExecutionWithoutSideEffects()
    {
        $package = self::$files->directory('package-desynchronized');
        $app     = $this->app($package);

        $this->assertNotEquals(0, $app->run('update', $this->updateOptions));
        $this->assertSameFiles($package, 'package-desynchronized');
    }

    protected function assertSameFiles(Directory $package, string $fixturesDirectory): void
    {
        $expected   = self::$files->directory($fixturesDirectory);
        $givenFiles = $package->files();
        $this->assertCount(count($expected->files()), $givenFiles, 'Different number of files');

        foreach ($givenFiles as $file) {
            $message = 'Contents mismatch for file: ' . $file->name();
            $this->assertSame($expected->file($file->name())->contents(), $file->contents(), $message);
        }
    }

    protected function app(Directory $packageDir, ?bool $forceMetaFile = null, bool $backupExists = false): Application
    {
        if (!is_null($forceMetaFile)) {
            $metaFile = $forceMetaFile ? new Doubles\MockedFile() : new Doubles\MockedFile(null);
        }

        $env = new RuntimeEnv(
            $packageDir,
            self::$skeleton,
            new Doubles\MockedTerminal(),
            $backupExists ? $this->backupFiles() : null,
            $metaFile ?? null
        );

        $replacements = $env->replacements();
        $replacements->add(self::PACKAGE_NAME, new Replacement\PackageName($env));
        $replacements->add(self::REPO_NAME, new Replacement\RepositoryName($env, self::PACKAGE_NAME));
        $replacements->add(self::PACKAGE_DESC, new Replacement\PackageDescription($env, self::PACKAGE_NAME));
        $replacements->add(self::SRC_NAMESPACE, new Replacement\SrcNamespace($env, self::PACKAGE_NAME));

        $templates = $env->templates();
        $templates->add('composer.json', new Template\Factory\MergedJsonFactory($env));

        return new Application($env);
    }

    private function backupFiles(): Doubles\FakeDirectory
    {
        $backup = new Doubles\FakeDirectory();
        $backup->addFile('README.md', 'anything');
        return $backup;
    }
}
