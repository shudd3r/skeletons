<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Replacement;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Replacement\RepositoryName;
use Shudd3r\PackageFiles\Application\Token;
use Shudd3r\PackageFiles\ReplacementReader;
use Shudd3r\PackageFiles\Tests\Doubles;


class RepositoryNameTest extends TestCase
{
    public function testInputNames()
    {
        $replacement = new RepositoryName();
        $this->assertSame('repo', $replacement->optionName());
        $this->assertSame('Github repository name', $replacement->inputPrompt());
    }

    public function testWithoutGitConfigFile_DefaultValue_IsResolvedFromFallbackReplacement()
    {
        $replacement = new RepositoryName('fallback.name');
        $env         = new Doubles\FakeRuntimeEnv();

        $fakeReplacement = new ReplacementReader($env, new Doubles\FakeReplacement('fallback/name'));
        $fallback        = new Token\Replacements([], ['fallback.name' => $fakeReplacement]);

        $this->assertSame('fallback/name', $replacement->defaultValue($env, [], $fallback));
    }

    public function testWithoutRemoteRepositoriesInGitConfig_DefaultValue_IsResolvedFromFallbackReplacement()
    {
        $replacement = new RepositoryName('fallback.name');
        $env         = new Doubles\FakeRuntimeEnv();
        $env->package()->addFile('.git/config', $this->config());

        $fakeReplacement = new ReplacementReader($env, new Doubles\FakeReplacement('fallback/name'));
        $fallback        = new Token\Replacements([], ['fallback.name' => $fakeReplacement]);

        $this->assertSame('fallback/name', $replacement->defaultValue($env, [], $fallback));
    }

    public function testWithRemoteRepositoriesInGitConfig_DefaultValue_IsReadWithCorrectPriority()
    {
        $replacement = new RepositoryName();
        $env         = new Doubles\FakeRuntimeEnv();
        $fallback    = new Token\Replacements([]);
        $gitConfig   = $env->package()->file('.git/config');

        $remotes = ['foo' => 'git@github.com:some/repo.git'];
        $gitConfig->write($this->config($remotes));
        $this->assertSame('some/repo', $replacement->defaultValue($env, [], $fallback));

        $remotes += ['second' => 'git@github.com:other/repo.git'];
        $gitConfig->write($this->config($remotes));
        $this->assertSame('some/repo', $replacement->defaultValue($env, [], $fallback));

        $remotes += ['origin' => 'https://github.com/orig/repo.git'];
        $gitConfig->write($this->config($remotes));
        $this->assertSame('orig/repo', $replacement->defaultValue($env, [], $fallback));

        $remotes += ['upstream' => 'git@github.com:master/ssh-repo.git'];
        $gitConfig->write($this->config($remotes));
        $this->assertSame('master/ssh-repo', $replacement->defaultValue($env, [], $fallback));
    }

    public function testTokenMethodWithValidValue_ReturnsExpectedToken()
    {
        $replacement = new RepositoryName();
        $expected    = new Token\ValueToken('repo.name', 'repository/name');
        $this->assertEquals($expected, $replacement->token('repo.name', 'repository/name'));
        $this->assertTrue($replacement->isValid('repository/name'));
    }

    /**
     * @dataProvider valueExamples
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testTokenFactoryMethods_ValidatesValue(string $invalid, string $valid)
    {
        $replacement = new RepositoryName();

        $this->assertFalse($replacement->isValid($invalid));
        $this->assertNull($replacement->token('repo.name', $invalid));

        $this->assertTrue($replacement->isValid($valid));
        $this->assertInstanceOf(Token::class, $replacement->token('repo.name', $valid));
    }

    public function valueExamples(): array
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

    private function config(array $remotes = []): string
    {
        $remoteConfig = '';
        foreach ($remotes as $name => $url) {
            $remoteConfig .= <<<INI
                [remote "$name"]
                    url = $url
                    fetch = +refs/heads/*:refs/remotes/$name/*
                
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
            $remoteConfig
            [branch "develop"]
                remote = origin
                merge = refs/heads/develop
            
            INI;
    }
}
