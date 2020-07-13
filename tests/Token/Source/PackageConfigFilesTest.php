<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Token\Source;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Token\Source\PackageConfigFiles;
use Shudd3r\PackageFiles\Tests\Doubles;
use RuntimeException;


class PackageConfigFilesTest extends TestCase
{
    private ?Doubles\FakeDirectory $directory;

    public function testGithubUrlIsReadFromGitConfigFile()
    {
        $properties = $this->properties();
        $this->assertSame('', $properties->repositoryName());

        $this->directory->files['.git/config'] = new Doubles\MockedFile(
            <<<'INI'
            [core]
                repositoryformatversion = 0
                filemode = false
                bare = false
                logallrefupdates = true
                symlinks = false
                ignorecase = true
            [remote "origin"]
                url = https://github.com/username/repository.git
                fetch = +refs/heads/*:refs/remotes/origin/*
            [branch "develop"]
                remote = origin
                merge = refs/heads/develop
            
            INI
        );

        $this->assertSame('username/repository', $properties->repositoryName());
    }

    public function testGithubUrlForUpstreamRepositoryTakesPrecedence()
    {
        $properties = $this->properties();
        $this->directory->files['.git/config'] = new Doubles\MockedFile(
            <<<'INI'
            [remote "origin"]
                url = https://github.com/username/repositoryOrigin.git
                fetch = +refs/heads/*:refs/remotes/origin/*
            [remote "upstream"]
                url = https://github.com/username/repositoryUpstream.git
                fetch = +refs/heads/*:refs/remotes/origin/*
            INI
        );

        $this->assertSame('username/repositoryUpstream', $properties->repositoryName());
    }

    public function testValuesReadFromComposerJsonFile()
    {
        $properties = $this->properties();
        $this->assertSame('', $properties->packageName());
        $this->assertSame('', $properties->packageDescription());
        $this->assertSame('', $properties->sourceNamespace());

        $properties = $this->properties();
        $this->directory->files['composer.json'] = new Doubles\MockedFile(
            <<<'JSON'
            {
                "name": "package/name",
                "description": "Test description",
                "autoload": {
                    "psr-4": {
                        "Foo\\Bar\\": "src/",
                        "Something\\Else\\": "test/"
                    }
                }
            }
            
            JSON
        );

        $this->assertSame('package/name', $properties->packageName());
        $this->assertSame('Test description', $properties->packageDescription());
        $this->assertSame('Foo\\Bar', $properties->sourceNamespace());
    }

    public function testInvalidComposerJsonFile_ThrowsException()
    {
        $properties = $this->properties();
        $this->directory->files['composer.json'] = new Doubles\MockedFile('Not json format');

        $this->expectException(RuntimeException::class);
        $properties->packageName();
    }

    private function properties()
    {
        return new PackageConfigFiles($this->directory ??= new Doubles\FakeDirectory());
    }
}
