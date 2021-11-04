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
use Shudd3r\Skeletons\Environment\FileSystem\Directory;


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
        $this->assertSameFiles($package, 'package', true);
    }

    public function testInitializationOverwritingBackupFile_AbortsExecutionWithoutSideEffects()
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package, null, true);

        $this->assertNotEquals(0, $app->run('init', $this->initOptions));
        $this->assertSameFiles($package, 'package');
    }

    public function testInitializeWithInvalidReplacements_AbortsExecutionWithoutSideEffects()
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package);

        $this->assertNotEquals(0, $app->run('init', ['package' => 'invalid-package-name'] + $this->initOptions));
        $this->assertSameFiles($package, 'package');
    }

    public function testInitializedPackageWithLocalFiles_IsValidForLocalCheck()
    {
        $app = $this->app(self::$files->directory('package-initialized'));
        $this->assertSame(0, $app->run('check'));
    }

    public function testSynchronizedPackageWithoutLocalFiles_IsValidForRemoteCheck()
    {
        $app = $this->app(self::$files->directory('package-synchronized'));
        $this->assertSame(0, $app->run('check', ['remote' => true]));
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

    public function testUpdatingWithInvalidReplacements_AbortsExecutionWithoutSideEffects()
    {
        $package = self::$files->directory('package-synchronized');
        $app     = $this->app($package);

        $this->assertNotEquals(0, $app->run('update', ['package' => 'invalid-package-name'] + $this->updateOptions));
        $this->assertSameFiles($package, 'package-synchronized');
    }

    protected function assertSameFiles(Directory $package, string $fixturesDirectory, bool $addMetaFile = false): void
    {
        $expected = self::$files->directory($fixturesDirectory);
        if ($addMetaFile) { $this->addMetaFile($expected); }

        $givenFiles = $package->fileList();
        $this->assertCount(count($expected->fileList()), $givenFiles, 'Different number of files');

        foreach ($givenFiles as $file) {
            $filename = str_replace('.sk_dir', '', $file->name());
            $message = 'Contents mismatch for file: ' . $file->name();
            $this->assertSame($expected->file($filename)->contents(), $file->contents(), $message);
        }
    }

    protected function app(Directory $packageDir, ?bool $forceMetaFile = null, bool $backupExists = false): Application
    {
        $app = new Application($packageDir, self::$skeleton, new Doubles\MockedTerminal());

        if ($backupExists) { $app->backup($this->backupDirectory()); }
        if ($forceMetaFile) { $this->addMetaFile($packageDir); }
        if (!is_null($forceMetaFile)) { $app->metaFile('forced/metaFile.json'); }

        $app->replacement(self::PACKAGE_NAME)->add(new Replacement\PackageName());
        $app->replacement(self::REPO_NAME)->add(new Replacement\RepositoryName(self::PACKAGE_NAME));
        $app->replacement(self::PACKAGE_DESC)->add(new Replacement\PackageDescription(self::PACKAGE_NAME));
        $app->replacement(self::SRC_NAMESPACE)->add(new Replacement\SrcNamespace(self::PACKAGE_NAME));

        $app->template('composer.json')->add(new MergedJsonFactory());

        return $app;
    }

    private function backupDirectory(): Directory
    {
        $backup = new Doubles\FakeDirectory();
        $backup->addFile('README.md', 'anything');
        return $backup;
    }

    private function addMetaFile(Directory $directory): void
    {
        $directory->file('forced/metaFile.json')->write('something');
    }
}
