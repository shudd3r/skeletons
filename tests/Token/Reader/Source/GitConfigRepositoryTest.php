<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Token\Reader\Source;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Token\Reader\Source\GitConfigRepository;
use Shudd3r\PackageFiles\Tests\Doubles;


class GitConfigRepositoryTest extends TestCase
{
    public function testMissingConfigFile_ReturnsEmptyString()
    {
        $this->assertSame('', $this->source()->value());
    }

    public function testConfigFileWithoutRemoteDefinitions_ReturnsEmptyString()
    {
        $this->assertSame('', $this->source([])->value());
    }

    public function testDefinedRemote_ReturnsRepositoryNameFromUrl()
    {
        $remotes = ['origin' => 'https://github.com/username/repository.git'];
        $this->assertSame('username/repository', $this->source($remotes)->value());

        $remotes = ['upstream' => 'https://github.com/upstream/repository.git'];
        $this->assertSame('upstream/repository', $this->source($remotes)->value());
    }

    public function testUpstreamNameTakesPrecedenceOverOrigin()
    {
        $remotes = [
            'origin'   => 'https://github.com/origin/repo.git',
            'upstream' => 'https://github.com/upstream/repo.git'
        ];

        $this->assertSame('upstream/repo', $this->source($remotes)->value());
    }

    private function source(array $remotes = null)
    {
        $directory = new Doubles\FakeDirectory();
        if (is_array($remotes)) {
            $configFile = new Doubles\MockedFile($this->config($remotes));
            $directory->files['.git/config'] = $configFile;
        }
        return new GitConfigRepository($directory);
    }

    private function config(array $remotes = []): string
    {
        $remoteConfig = '';
        foreach ($remotes as $name => $url) {
            $remoteConfig .= <<<INI
                [remote "{$name}"]
                    url = {$url}
                    fetch = +refs/heads/*:refs/remotes/{$name}/*
                INI;
        }

        return <<<INI
            [core]
                repositoryformatversion = 0
                filemode = false
                bare = false
                logallrefupdates = true
                symlinks = false
                ignorecase = true
            {$remoteConfig}
            [branch "develop"]
                remote = origin
                merge = refs/heads/develop
            
            INI;
    }
}
