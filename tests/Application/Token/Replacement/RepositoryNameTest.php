<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Token\Replacement;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Token\Replacement\RepositoryName;
use Shudd3r\PackageFiles\Application\Token\Replacement\PackageName;
use Shudd3r\PackageFiles\Application\Token;
use Shudd3r\PackageFiles\Tests\Doubles;


class RepositoryNameTest extends TestCase
{
    public function testWithoutGitConfigFile_InitialTokenValue_IsResolvedFromPackageName()
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->package()->path = 'some/directory/package/name';

        $replacement = $this->replacement($env);
        $this->assertToken($replacement->initialToken('repo.placeholder', []), 'package/name', 'repo.placeholder');
    }

    public function testWithoutRemoteRepositoriesInGitConfig_InitialTokenValue_IsResolvedFromPackageName()
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->package()->file('.git/config')->write($this->config());
        $env->package()->path = 'some/directory/package/name';

        $replacement = $this->replacement($env);
        $this->assertToken($replacement->initialToken('repo.placeholder', []), 'package/name', 'repo.placeholder');
    }

    public function testWithRemoteRepositoriesInGitConfig_InitialTokenValue_IsReadWithCorrectPriority()
    {
        $env = new Doubles\FakeRuntimeEnv();

        $remotes = ['foo' => 'git@github.com:some/repo.git'];
        $env->package()->file('.git/config')->write($this->config($remotes));
        $replacement = $this->replacement($env);
        $this->assertToken($replacement->initialToken('repo.name', []), 'some/repo');

        $remotes += ['second' => 'git@github.com:other/repo.git'];
        $env->package()->file('.git/config')->write($this->config($remotes));
        $replacement = $this->replacement($env);
        $this->assertToken($replacement->initialToken('repo.name', []), 'some/repo');

        $remotes += ['origin' => 'https://github.com/orig/repo.git'];
        $env->package()->file('.git/config')->write($this->config($remotes));
        $replacement = $this->replacement($env);
        $this->assertToken($replacement->initialToken('repo.name', []), 'orig/repo');

        $remotes += ['upstream' => 'git@github.com:master/ssh-repo.git'];
        $env->package()->file('.git/config')->write($this->config($remotes));
        $replacement = $this->replacement($env);
        $this->assertToken($replacement->initialToken('repo.name', []), 'master/ssh-repo');
    }

    public function testTokenFactoryMethods_CreateCorrectToken()
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->metaDataFile()->contents = '{"repo.name": "meta/name"}';
        $env->package()->path = 'root/directory/init/name';

        $replacement = $this->replacement($env);
        $this->assertToken($replacement->initialToken('repo.name', []), 'init/name');
        $this->assertToken($replacement->validationToken('repo.name'), 'meta/name');
        $this->assertToken($replacement->updateToken('repo.name', []), 'meta/name');
    }

    /**
     * @dataProvider valueExamples
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testTokenFactoryMethods_ValidateTokenValue(string $invalid, string $valid)
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->metaDataFile()->contents = '{"repo.name": "' . $invalid . '"}';
        $options     = ['repo' => $invalid];
        $replacement = $this->replacement($env);
        $this->assertNull($replacement->initialToken('repo.name', $options));
        $this->assertNull($replacement->validationToken('repo.name'));
        $this->assertNull($replacement->updateToken('repo.name', $options));

        $env = new Doubles\FakeRuntimeEnv();
        $env->metaDataFile()->contents = '{"repo.name": "' . $valid . '"}';
        $options     = ['repo' => $valid];
        $replacement = $this->replacement($env);
        $this->assertInstanceOf(Token::class, $replacement->initialToken('repo.name', $options));
        $this->assertInstanceOf(Token::class, $replacement->validationToken('repo.name'));
        $this->assertInstanceOf(Token::class, $replacement->updateToken('repo.name', $options));
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

    private function assertToken(Token $token, string $value, string $name = 'repo.name')
    {
        $expected = new Token\ValueToken($name, $value);
        $this->assertEquals($expected, $token);
    }

    private function replacement(Doubles\FakeRuntimeEnv $env): RepositoryName
    {
        return new RepositoryName($env, new PackageName($env));
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
