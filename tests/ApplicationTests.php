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
use Shudd3r\Skeletons\InputArgs;
use Shudd3r\Skeletons\Replacements\Replacement;
use Shudd3r\Skeletons\Templates\Factory\MergedJsonFactory;
use Shudd3r\Skeletons\Environment\Files\Directory;
use Shudd3r\Skeletons\Environment\Files\File;


class ApplicationTests extends TestCase
{
    protected const PACKAGE_NAME  = 'package.name';
    protected const PACKAGE_DESC  = 'package.description';
    protected const SRC_NAMESPACE = 'namespace.src';
    protected const REPO_NAME     = 'repository.name';
    protected const AUTHOR_EMAIL  = 'author.email';

    protected static Fixtures\ExampleFiles      $files;
    protected static Directory\VirtualDirectory $skeleton;
    protected static Doubles\MockedTerminal     $terminal;

    public static function setUpBeforeClass(): void
    {
        self::$files    = new Fixtures\ExampleFiles('example-files');
        self::$skeleton = self::$files->directory('template');
        self::$terminal = new Doubles\MockedTerminal();
    }

    public function fileContentsBackupStrategy(): array
    {
        return [
            'mismatched' => ['{}', true],
            'matched'    => ['---match---', false],
            'empty'      => ['', false]
        ];
    }

    protected function assertSameFiles(Directory $package, string $fixturesDirectory): void
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

        $app->replacement(self::PACKAGE_NAME)
            ->add(new Replacement\PackageName());
        $app->replacement(self::REPO_NAME)
            ->add(new Replacement\RepositoryName(self::PACKAGE_NAME));
        $app->replacement(self::PACKAGE_DESC)
            ->add(new Replacement\PackageDescription(self::PACKAGE_NAME));
        $app->replacement(self::SRC_NAMESPACE)
            ->add(new Replacement\SrcNamespace(self::PACKAGE_NAME));
        $app->replacement(self::AUTHOR_EMAIL)
            ->build(fn () => 'default@example.com')
            ->argumentName('email')
            ->inputPrompt('Your email address')
            ->validate(fn (string $value) => $value === filter_var($value, FILTER_VALIDATE_EMAIL));

        $app->template('composer.json')->add(new MergedJsonFactory($isUpdate));

        return $app;
    }

    protected function args(string ...$args): InputArgs
    {
        return new InputArgs(['script-name', ...$args]);
    }

    protected function snapshot(Directory $directory): array
    {
        return array_map(fn (File $file) => $file->contents(), $directory->fileList());
    }
}
