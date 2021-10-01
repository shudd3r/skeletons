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
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Application\Template;
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
        $env = $this->envSetup('package');
        $app = new Application($env);

        $this->assertNotEquals(0, $app->run('unknown', []));
    }

    public function testInitialization_GeneratesFilesFromTemplate()
    {
        $env = $this->envSetup('package');
        $app = new Application($env);

        $this->assertSame(0, $app->run('init', $this->initOptions));
        $this->assertSameFiles($env, 'package-initialized');
    }

    public function testInitializationWithExistingMetaDataFile_AbortsExecutionWithoutSideEffects()
    {
        $env = $this->envSetup('package', new Doubles\MockedFile());
        $app = new Application($env);

        $this->assertNotEquals(0, $app->run('init', $this->initOptions));
        $this->assertSameFiles($env, 'package');
    }

    public function testInitializationOverwritingBackupFile_AbortsExecutionWithoutSideEffects()
    {
        $env = $this->envSetup('package', null, true);
        $app = new Application($env);

        $this->assertNotEquals(0, $app->run('init', $this->initOptions));
        $this->assertSameFiles($env, 'package');
    }

    public function testInitializedPackage_IsValid()
    {
        $env = $this->envSetup('package-initialized');
        $app = new Application($env);

        $this->assertSame(0, $app->run('check'));
    }

    public function testSynchronizedPackage_IsValid()
    {
        $env = $this->envSetup('package-synchronized');
        $app = new Application($env);

        $this->assertSame(0, $app->run('check'));
    }

    public function testDesynchronizedPackage_IsInvalid()
    {
        $env = $this->envSetup('package-desynchronized');
        $app = new Application($env);

        $this->assertNotEquals(0, $app->run('check'));
    }

    public function testPackageWithoutMetaDataFile_IsInvalid()
    {
        $env = $this->envSetup('package-synchronized', new Doubles\MockedFile(null));
        $app = new Application($env);

        $this->assertNotEquals(0, $app->run('check'));
    }

    public function testUpdatingSynchronizedPackage_GeneratesUpdatedPackageFiles()
    {
        $env = $this->envSetup('package-synchronized');
        $app = new Application($env);

        $this->assertSame(0, $app->run('update', $this->updateOptions));
        $this->assertSameFiles($env, 'package-updated');
    }

    public function testUpdatingPackageWithoutMetaDataFile_AbortsExecutionWithoutSideEffects()
    {
        $env = $this->envSetup('package-synchronized', new Doubles\MockedFile(null));
        $app = new Application($env);

        $this->assertNotEquals(0, $app->run('update', $this->updateOptions));
        $this->assertSameFiles($env, 'package-synchronized');
    }

    public function testUpdatingDesynchronizedPackage_AbortsExecutionWithoutSideEffects()
    {
        $env = $this->envSetup('package-desynchronized');
        $app = new Application($env);

        $this->assertNotEquals(0, $app->run('update', $this->updateOptions));
        $this->assertSameFiles($env, 'package-desynchronized');
    }

    protected function assertSameFiles(RuntimeEnv $env, string $fixturesDirectory): void
    {
        $given    = $env->package();
        $expected = self::$files->directory($fixturesDirectory);

        $givenFiles = $given->files();
        $this->assertCount(count($expected->files()), $givenFiles, 'Different number of files');

        foreach ($givenFiles as $file) {
            $message = 'Contents mismatch for file: ' . $file->name();
            $this->assertSame($expected->file($file->name())->contents(), $file->contents(), $message);
        }
    }

    protected function envSetup(string $packageDir, ?File $metaFile = null, bool $backupExists = false): RuntimeEnv
    {
        $env = new RuntimeEnv(
            self::$files->directory($packageDir),
            self::$skeleton,
            new Doubles\MockedTerminal(),
            $backupExists ? $this->backupFiles() : null,
            $metaFile
        );

        $replacements = $env->replacements();
        $replacements->add(self::PACKAGE_NAME, new Replacement\PackageName($env));
        $replacements->add(self::REPO_NAME, new Replacement\RepositoryName($env, self::PACKAGE_NAME));
        $replacements->add(self::PACKAGE_DESC, new Replacement\PackageDescription($env, self::PACKAGE_NAME));
        $replacements->add(self::SRC_NAMESPACE, new Replacement\SrcNamespace($env, self::PACKAGE_NAME));

        $templates = $env->templates();
        $templates->add('composer.json', new Template\Factory\MergedJsonFactory($env));

        return $env;
    }

    private function backupFiles(): Doubles\FakeDirectory
    {
        $backup = new Doubles\FakeDirectory();
        $backup->addFile('README.md', 'anything');
        return $backup;
    }
}
