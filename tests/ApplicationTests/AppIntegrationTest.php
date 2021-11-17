<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\ApplicationTests;

use Shudd3r\Skeletons\Tests\ApplicationTests;


class AppIntegrationTest extends ApplicationTests
{
    public function testUnknownCommand_ReturnsErrorCode()
    {
        $app = $this->app(self::$files->directory());
        $this->assertSame(128, $app->run($this->args('unknown')));
    }

    public function testWithBackupDirectorySet_BackupFilesAreCopiedToThatDirectory()
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package);
        $backup  = self::$files->directory();

        $app->backup($backup);
        $this->assertFalse($backup->file('README.md')->exists());
        $this->assertFalse($backup->file('composer.json')->exists());

        $app->run($this->args('init'));
        $this->assertTrue($backup->file('README.md')->exists());
        $this->assertTrue($backup->file('composer.json')->exists());
    }

    public function testWithMetaDataFilenameSet_MetaDataIsSavedInThatFileInsidePackageDirectory()
    {
        $package = self::$files->directory();
        $app     = $this->app($package);

        $app->metaFile('dev/meta-data.json');
        $this->assertFalse($package->file('dev/meta-data.json')->exists());

        $app->run($this->args('init'));
        $this->assertTrue($package->file('dev/meta-data.json')->exists());
    }

    public function testWithoutOptionValues_ReplacementsAreTakenFromInput()
    {
        $package = self::$files->directory();
        $app = $this->app($package);

        $app->metaFile('dev/meta-date.json');
        $expected = [
            self::PACKAGE_NAME  => 'input/package',
            self::REPO_NAME     => 'input/repo',
            self::PACKAGE_DESC  => 'input description',
            self::SRC_NAMESPACE => 'Input\\Namespace',
            self::AUTHOR_EMAIL  => 'input@example.com'
        ];
        $this->addInputs($expected);

        $app->run($this->args('init', '-i'));
        $this->assertSame($expected, json_decode($package->file('dev/meta-date.json')->contents(), true));
    }

    public function testWithoutInput_ReplacementsAreResolvedFromDefaultsAndFallbacks()
    {
        $package = self::$files->directory('not/exists/package/directory');
        $app = $this->app($package);

        $app->metaFile('dev/meta-date.json');
        $expected = [
            self::PACKAGE_NAME  => 'package/directory',
            self::REPO_NAME     => 'input/repo',
            self::PACKAGE_DESC  => 'package/directory package',
            self::SRC_NAMESPACE => 'Input\\Namespace',
            self::AUTHOR_EMAIL  => 'default@example.com'
        ];

        $emptyInputs = [self::PACKAGE_NAME => '', self::PACKAGE_DESC => '', self::AUTHOR_EMAIL => ''];
        $this->addInputs(array_merge($expected, $emptyInputs));

        $app->run($this->args('init', '-i'));
        $this->assertSame($expected, json_decode($package->file('dev/meta-date.json')->contents(), true));
    }

    public function testDisplaysSkeletonNameInScriptHeader()
    {
        $app = $this->app(self::$files->directory());

        $app->run($this->args('help'));
        $messages = self::$terminal->messagesSent();
        $this->assertStringContainsString('Script-name', $messages[0]);

        $app->skeletonName('My Super Skeleton');

        $app->run($this->args('help'));
        $messages = self::$terminal->messagesSent();
        $this->assertStringNotContainsString('Script-name', $messages[0]);
        $this->assertStringContainsString('My Super Skeleton', $messages[0]);
    }

    private function addInputs(array $inputs): void
    {
        array_walk($inputs, fn (string $inputValue) => self::$terminal->addInput($inputValue));
    }
}
