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
use Shudd3r\PackageFiles\Token;
use Shudd3r\PackageFiles\Token\Reader;
use Shudd3r\PackageFiles\Tests\Doubles;


class RepositoryNameTest extends TestCase
{
    public function testInstantiation()
    {
        $reader = $this->reader('repo/name');
        $this->assertInstanceOf(Reader\ValueToken::class, $reader);
    }

    public function testReaderWithEmptyConfigName_ParsedValueMethod_ResolvesNameFromPackageName()
    {
        $reader = $this->reader(null, false);
        $this->assertSame('package/name', $reader->parsedValue());
    }

    public function testReaderWithConfigName_ParsedValueMethod_ResolvesNameFromConfig()
    {
        $reader = $this->reader(null);
        $this->assertSame('config/repo', $reader->parsedValue());
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

    public function testReader_TokenMethod_ReturnsCorrectToken()
    {
        $expected = new Token\ValueToken('{repository.name}', 'source/repo');
        $this->assertEquals($expected, $this->reader('source/repo')->token());
    }

    /**
     * @dataProvider valueExamples
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testReaderValueValidation(string $invalid, string $valid)
    {
        $reader = $this->reader($invalid);
        $this->assertSame($invalid, $reader->value());
        $this->assertNull($reader->token());

        $reader = $this->reader($valid);
        $this->assertSame($valid, $reader->value());
        $this->assertInstanceOf(Token::class, $reader->token());
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

    private function reader(?string $source, bool $config = true): Reader\RepositoryName
    {
        $config  = $config ? ['origin' => 'https://github.com/config/repo.git'] : [];
        $config  = new Doubles\MockedFile($this->config($config));
        $package = new Doubles\FakePackageName('package/name');

        return isset($source)
            ? new Reader\RepositoryName($config, $package, new Doubles\FakeSourceV2($source))
            : new Reader\RepositoryName($config, $package);
    }

    private function configReader(array $config = []): Reader\RepositoryName
    {
        $config  = new Doubles\MockedFile($this->config($config));
        $package = new Doubles\FakePackageName('package/name');

        return new Reader\RepositoryName($config, $package);
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
