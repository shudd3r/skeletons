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
use Shudd3r\PackageFiles\Token;
use Shudd3r\PackageFiles\Tests\Doubles;


class DefaultRepositoryTest extends TestCase
{
    /**
 * @dataProvider valueExamples
 *
 * @param string $invalid
 * @param string $valid
 */
    public function testInvalidReaderValue_ReturnsNull(string $invalid, string $valid)
    {
        $this->assertInstanceOf(Token::class, $this->reader(false)->create($valid));
        $this->assertNull($this->reader(false)->create($invalid));
    }

    public function valueExamples()
    {
        $name = function (int $length) { return str_pad('x', $length, 'x'); };

        $longAccount  = $name(40) . '/name';
        $shortAccount = $name(39) . '/name';
        $longRepo     = 'user/' . $name(101);
        $shortRepo    = 'user/' . $name(100);

        return [
            ['repo/na(me)', 'repo/na-me'],
            ['-repo/name', 'r-epo/name'],
            ['repo_/name', 'repo/name'],
            ['re--po/name', 're-po/name'],
            [$longAccount, $shortAccount],
            [$longRepo, $shortRepo]
        ];
    }

    public function testValue_ReturnsGitConfigRemotePath()
    {
        $this->assertSame('config/repo', $this->reader()->value());
    }

    public function testForMissingConfig_Value_ReturnsFallbackValue()
    {
        $this->assertSame('package/name', $this->reader(false)->value());
    }

    public function testForMissingRemoteInGitConfig_Value_ReturnsFallbackValue()
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

    private function reader(bool $config = true): Token\Reader\Source\DefaultRepository
    {
        $config   = $config ? ['origin' => 'https://github.com/config/repo.git'] : [];
        $config   = new Doubles\MockedFile($this->config($config));
        return new Token\Reader\Source\DefaultRepository($config, new Doubles\FakeSource('package/name'));
    }

    private function configReader(array $config = []): Token\Reader\Source\DefaultRepository
    {
        $config   = new Doubles\MockedFile($this->config($config));
        return new Token\Reader\Source\DefaultRepository($config, new Doubles\FakeSource('package/name'));
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
