<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Token\Reader;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Token\Reader\RepositoryReader;
use Shudd3r\PackageFiles\Token\Repository;
use Shudd3r\PackageFiles\Tests\Doubles;


class RepositoryReaderTest extends TestCase
{
    public function testTokenCreatedFromFallbackValue()
    {
        $this->assertSame('fallback/repo', $this->reader(false)->value());
        $this->assertEquals(new Repository('fallback/repo'), $this->reader(false)->token());
    }

    public function testTokenCreatedFromConfigValue()
    {
        $this->assertSame('config/repo', $this->reader(true)->value());
        $this->assertEquals(new Repository('config/repo'), $this->reader(true)->token());
    }

    public function testTokenCreatedFromParameterValue()
    {
        $this->assertEquals(new Repository('repository/foo'), $this->reader(true)->createToken('repository/foo'));
        $this->assertEquals(new Repository('repository/foo'), $this->reader(false)->createToken('repository/foo'));
    }

    public function testConfigParsingPriority()
    {
        $reader = $this->configReader($config = []);
        $this->assertSame('fallback/repo', $reader->value());

        $reader = $this->configReader($config += ['foo' => 'git@github.com:other/repo.git']);
        $this->assertSame('other/repo', $reader->value());

        $reader = $this->configReader($config += ['origin' => 'https://github.com/orig/repo.git']);
        $this->assertSame('orig/repo', $reader->value());

        $reader = $this->configReader($config + ['upstream' => 'git@github.com:master/ssh-repo.git']);
        $this->assertSame('master/ssh-repo', $reader->value());
    }

    private function reader(bool $config): RepositoryReader
    {
        $config   = $config ? ['origin' => 'https://github.com/config/repo.git'] : [];
        $config   = new Doubles\MockedFile($this->config($config), (bool) $config);
        $fallback = new Doubles\FakeValueReader('fallback/repo');

        return new RepositoryReader($config, $fallback);
    }

    private function configReader(array $config = []): RepositoryReader
    {
        return new RepositoryReader(
            new Doubles\MockedFile($this->config($config)),
            new Doubles\FakeValueReader('fallback/repo')
        );
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
