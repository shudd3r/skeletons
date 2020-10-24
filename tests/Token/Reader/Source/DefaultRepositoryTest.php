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
use Shudd3r\PackageFiles\Token\Reader\Source\DefaultRepository;
use Shudd3r\PackageFiles\Token\Reader\PackageReader;
use Shudd3r\PackageFiles\Tests\Doubles;


class DefaultRepositoryTest extends TestCase
{
    public function testValue_ReturnsGitConfigRemotePath()
    {
        $this->assertSame('config/repo', $this->reader()->value());
    }

    public function testForMissingConfig_Value_ReturnsPackageName()
    {
        $this->assertSame('package/name', $this->reader(false)->value());
    }

    public function testForMissingRemoteInGitConfig_Value_ReturnsPackageName()
    {
        $this->assertSame('package/name', $this->configReader()->value());
    }

    public function testPathPriorityForMultipleRemotesInGitConfig()
    {
        $reader = $this->configReader($config = []);
        $this->assertSame('package/name', $reader->value());

        $reader = $this->configReader($config += ['foo' => 'git@github.com:other/repo.git']);
        $this->assertSame('other/repo', $reader->value());

        $reader = $this->configReader($config += ['origin' => 'https://github.com/orig/repo.git']);
        $this->assertSame('orig/repo', $reader->value());

        $reader = $this->configReader($config + ['upstream' => 'git@github.com:master/ssh-repo.git']);
        $this->assertSame('master/ssh-repo', $reader->value());
    }

    private function reader(bool $config = true): DefaultRepository
    {
        $config   = $config ? ['origin' => 'https://github.com/config/repo.git'] : [];
        $config   = new Doubles\MockedFile($this->config($config));
        $fallback = new PackageReader(new Doubles\FakeSource('package/name'));

        return new DefaultRepository($config, $fallback);
    }

    private function configReader(array $config = []): DefaultRepository
    {
        $config   = new Doubles\MockedFile($this->config($config));
        $fallback = new PackageReader(new Doubles\FakeSource('package/name'));

        return new DefaultRepository($config, $fallback);
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
            {$remoteConfig}[branch "develop"]
                remote = origin
                merge = refs/heads/develop
            
            INI;
    }
}
