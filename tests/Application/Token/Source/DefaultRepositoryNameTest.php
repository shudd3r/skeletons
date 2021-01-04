<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Token\Source;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Token\Source\DefaultRepositoryName;
use Shudd3r\PackageFiles\Tests\Doubles;


class DefaultRepositoryNameTest extends TestCase
{
    public function testWithoutConfig_ValueMethod_ResolvesNameFromPackageName()
    {
        $this->assertSame('package/name', $this->source()->value(new Doubles\FakeValidator()));
        $this->assertSame('package/name', $this->source(null)->value(new Doubles\FakeValidator()));
    }

    public function testWithConfigName_ValueMethod_ResolvesNameFromConfig()
    {
        $config = ['origin' => 'https://github.com/config/repo.git'];
        $this->assertSame('config/repo', $this->source($config)->value(new Doubles\FakeValidator()));
    }

    public function testPathPriorityForMultipleRemotesInGitConfig()
    {
        $config = [];
        $this->assertSame('package/name', $this->source($config)->value(new Doubles\FakeValidator()));

        $config += ['foo' => 'git@github.com:other/repo.git'];
        $this->assertSame('other/repo', $this->source($config)->value(new Doubles\FakeValidator()));

        $config += ['origin' => 'https://github.com/orig/repo.git'];
        $this->assertSame('orig/repo', $this->source($config)->value(new Doubles\FakeValidator()));

        $config += ['upstream' => 'git@github.com:master/ssh-repo.git'];
        $this->assertSame('master/ssh-repo', $this->source($config)->value(new Doubles\FakeValidator()));
    }

    private function source(?array $config = []): DefaultRepositoryName
    {
        $config  = isset($config) ? $this->config($config) : '';
        $config  = new Doubles\MockedFile($config);
        $package = new Doubles\FakePackageName('package/name');

        return new DefaultRepositoryName($config, $package);
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
