<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Application;
use Shudd3r\Skeletons\Replacements\Replacement;
use Shudd3r\Skeletons\Templates\Factory\MergedJsonFactory;
use Shudd3r\Skeletons\Environment\Files\Directory;
use Shudd3r\Skeletons\Environment\Files\File;


class ApplicationTest extends TestCase
{
    private const PACKAGE_NAME  = 'package.name';
    private const PACKAGE_DESC  = 'package.description';
    private const SRC_NAMESPACE = 'namespace.src';
    private const REPO_NAME     = 'repository.name';

    private static Fixtures\ExampleFiles  $files;
    private static Doubles\FakeDirectory  $skeleton;
    private static Doubles\MockedTerminal $terminal;

    private array $initOptions = [
        'repo'    => 'initial/repo',
        'package' => 'initial/package-name',
        'desc'    => 'Initial package description',
        'ns'      => 'Package\Initial',
        'i'       => true
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
        self::$terminal = new Doubles\MockedTerminal();
    }

    public function testUnknownCommand_ReturnsErrorCode()
    {
        $app = $this->app(new Doubles\FakeDirectory());
        $this->assertNotEquals(0, $app->run('unknown', []));
    }

    public function testInitialization_GeneratesFilesFromTemplate()
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package);

        $this->assertSame(0, $app->run('init', $this->initOptions));
        $this->assertSameFiles($package, 'package-initialized');
    }

    public function testWithBackupDirectorySet_BackupFilesAreCopiedToThatDirectory()
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package);
        $backup  = new Doubles\FakeDirectory();

        $app->backup($backup);
        $app->run('init', $this->initOptions);

        $this->assertTrue($backup->file('README.md')->exists());
        $this->assertTrue($backup->file('composer.json')->exists());
    }

    public function testWithMetaDataFilenameSet_MetaDataIsSavedInThatFileInsidePackageDirectory()
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package);

        $app->metaFile('dev/meta-data.json');
        $app->run('init', $this->initOptions);

        $this->assertTrue($package->file('dev/meta-data.json')->exists());
    }

    public function testInitializationWithExistingMetaDataFile_AbortsExecutionWithoutSideEffects()
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package);

        $package->addFile('.package/skeleton.json');
        $app->metaFile('.package/skeleton.json');

        $expected = $this->snapshot($package);
        $this->assertNotEquals(0, $app->run('init', $this->initOptions));
        $this->assertSame($expected, $this->snapshot($package));
    }

    public function testInitializationOverwritingBackupFile_AbortsExecutionWithoutSideEffects()
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package);
        $backup  = new Doubles\FakeDirectory();

        $app->backup($backup);
        $backup->addFile('README.md');

        $expected = $this->snapshot($package);
        $this->assertNotEquals(0, $app->run('init', $this->initOptions));
        $this->assertSame($expected, $this->snapshot($package));
    }

    public function testInitializeWithInvalidReplacements_AbortsExecutionWithoutSideEffects()
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package);

        $expected = $this->snapshot($package);
        $this->assertNotEquals(0, $app->run('init', ['package' => 'invalid-package-name'] + $this->initOptions));
        $this->assertSame($expected, $this->snapshot($package));
    }

    public function testSynchronizedPackage_IsValidForLocalCheck()
    {
        $app = $this->app(self::$files->directory('package-initialized'));
        $this->assertSame(0, $app->run('check'));

        $app = $this->app(self::$files->directory('package-synchronized'));
        $this->assertSame(0, $app->run('check'));
    }

    public function testSynchronizedPackageWithoutLocalFiles_IsValidForRemoteCheck()
    {
        $package = self::$files->directory('package-synchronized');
        $app     = $this->app($package);

        $package->removeFile('.git/hooks/pre-commit');

        $this->assertSame(0, $app->run('check', ['remote' => true]));
        $this->assertNotSame(0, $app->run('check'));
    }

    public function testDesynchronizedPackage_IsInvalid()
    {
        $app = $this->app(self::$files->directory('package-desynchronized'));
        $this->assertNotEquals(0, $app->run('check'));
    }

    public function testPackageWithoutMetaDataFile_IsInvalid()
    {
        $package = self::$files->directory('package-synchronized');
        $app     = $this->app($package);

        $package->removeFile('.github/skeleton.json');
        $this->assertNotEquals(0, $app->run('check'));
    }

    public function testUpdatingSynchronizedPackage_GeneratesUpdatedPackageFiles()
    {
        $package = self::$files->directory('package-synchronized');
        $app     = $this->app($package, true);

        $this->assertSame(0, $app->run('update', $this->updateOptions));
        $this->assertSameFiles($package, 'package-updated');
    }

    public function testUpdatingPackageWithoutMetaDataFile_AbortsExecutionWithoutSideEffects()
    {
        $package = self::$files->directory('package-synchronized');
        $app     = $this->app($package, true);

        $package->removeFile('.github/skeleton.json');

        $expected = $this->snapshot($package);
        $this->assertNotEquals(0, $app->run('update', $this->updateOptions));
        $this->assertSame($expected, $this->snapshot($package));
    }

    public function testUpdatingDesynchronizedPackage_AbortsExecutionWithoutSideEffects()
    {
        $package = self::$files->directory('package-desynchronized');
        $app     = $this->app($package, true);

        $expected = $this->snapshot($package);
        $this->assertNotEquals(0, $app->run('update', $this->updateOptions));
        $this->assertSame($expected, $this->snapshot($package));
    }

    public function testUpdatingWithInvalidReplacements_AbortsExecutionWithoutSideEffects()
    {
        $package = self::$files->directory('package-synchronized');
        $app     = $this->app($package, true);

        $expected = $this->snapshot($package);
        $this->assertNotEquals(0, $app->run('update', ['package' => 'invalid-package-name'] + $this->updateOptions));
        $this->assertSame($expected, $this->snapshot($package));
    }

    public function testSynchronizingDesynchronizedPackage_GeneratesMissingAndDivergentFilesWithBackup()
    {
        $package = self::$files->directory('package-desynchronized');
        $app     = $this->app($package);

        $this->assertEquals(0, $app->run('sync'));
        $this->assertSameFiles($package, 'package-after-sync');
    }

    private function assertSameFiles(Directory $package, string $fixturesDirectory): void
    {
        $expected   = self::$files->directory($fixturesDirectory);
        $givenFiles = $package->fileList();
        $this->assertCount(count($expected->fileList()), $givenFiles, 'Different number of files');

        foreach ($givenFiles as $file) {
            $filename = str_replace('.sk_dir', '', $file->name());
            $message = 'Contents mismatch for file: ' . $file->name();
            $this->assertSame($expected->file($filename)->contents(), $file->contents(), $message);
        }
    }

    protected function app(Directory $packageDir, bool $isUpdate = false): Application
    {
        $app = new Application($packageDir, self::$skeleton, self::$terminal->reset());

        $app->replacement(self::PACKAGE_NAME)->add(new Replacement\PackageName());
        $app->replacement(self::REPO_NAME)->add(new Replacement\RepositoryName(self::PACKAGE_NAME));
        $app->replacement(self::PACKAGE_DESC)->add(new Replacement\PackageDescription(self::PACKAGE_NAME));
        $app->replacement(self::SRC_NAMESPACE)->add(new Replacement\SrcNamespace(self::PACKAGE_NAME));

        $app->template('composer.json')->add(new MergedJsonFactory($isUpdate));

        return $app;
    }

    private function snapshot(Directory $directory): array
    {
        return array_map(fn (File $file) => $file->contents(), $directory->fileList());
    }
}
