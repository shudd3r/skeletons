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
use Shudd3r\PackageFiles\Environment\Command;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Replacement;


abstract class IntegrationTestCase extends TestCase
{
    private const PACKAGE_NAME  = 'package.name';
    private const PACKAGE_DESC  = 'package.description';
    private const SRC_NAMESPACE = 'namespace.src';
    private const REPO_NAME     = 'repository.name';

    private static Fixtures\ExampleFiles $files;
    private static Doubles\FakeDirectory $skeleton;

    public static function setUpBeforeClass(): void
    {
        self::$files    = new Fixtures\ExampleFiles('example-files');
        self::$skeleton = self::$files->directory('template');
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

    abstract protected function command(RuntimeEnv $env): Command;

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
        $replacements->add(self::PACKAGE_NAME, $packageName = new Replacement\PackageName($env));
        $replacements->add(self::REPO_NAME, new Replacement\RepositoryName($env, $packageName));
        $replacements->add(self::PACKAGE_DESC, new Replacement\PackageDescription($env, $packageName));
        $replacements->add(self::SRC_NAMESPACE, new Replacement\SrcNamespace($env, $packageName));

        $jsonMerge = fn (Template $template, File $composer) => new Template\MergedJsonTemplate($template, $composer);
        $env->addTemplate('composer.json', $jsonMerge);

        return $env;
    }

    private function backupFiles(): Doubles\FakeDirectory
    {
        $backup = new Doubles\FakeDirectory();
        $backup->addFile('README.md', 'anything');
        return $backup;
    }
}
