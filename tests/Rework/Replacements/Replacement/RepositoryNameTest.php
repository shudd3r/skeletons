<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Rework\Replacements\Replacement;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Rework\Replacements\Replacement\RepositoryName;
use Shudd3r\Skeletons\Tests\Doubles\Rework\FakeSource as Source;
use Shudd3r\Skeletons\Replacements\Token;


class RepositoryNameTest extends TestCase
{
    public function testWithoutDataToResolveValue_TokenMethod_ReturnsNull()
    {
        $replacement = new RepositoryName('bar');

        $source = Source::create();
        $this->assertNull($replacement->token('foo', $source));
    }

    public function testWithValidFallbackValue_TokenValueIsResolvedFromThatValue()
    {
        $replacement = new RepositoryName('bar');

        $source = Source::create()->withFallbackTokenValue('bar', 'not repository name');
        $this->assertNull($replacement->token('foo', $source));

        $source = Source::create()->withFallbackTokenValue('bar', 'package/name');
        $this->assertToken('package/name', $replacement->token('foo', $source));
    }

    public function testWithGitConfigWithRemoteDefinitions_TokenValueIsResolvedFromMostAccurateOne()
    {
        $replacement = new RepositoryName('bar');

        $source     = Source::create()->withFallbackTokenValue('bar', 'fallback/name');
        $remoteList = [
            '--none--' => ['fallback/name', ''],
            'first'    => ['some/repo', 'git@github.com:some/repo.git'],
            'second'   => ['some/repo', 'git@github.com:other/repo.git'],
            'origin'   => ['orig/repo', 'https://github.com/orig/repo.git'],
            'upstream' => ['master/ssh-repo', 'git@github.com:master/ssh-repo.git']
        ];

        $remotes = [];
        foreach ($remoteList as $name => [$expected, $uri]) {
            if ($uri) { $remotes += [$name => $uri]; }
            $source = $source->withFileContents('.git/config', $this->config($remotes));
            $this->assertToken($expected, $replacement->token('foo', $source));
        }
    }

    /**
     * @dataProvider valueExamples
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testResolvedTokenValue_IsValidated(string $invalid, string $valid)
    {
        $replacement = new RepositoryName();

        $source = Source::create(['foo' => $valid, 'bar' => $invalid]);
        $this->assertToken($valid, $replacement->token('foo', $source));
        $this->assertNull($replacement->token('bar', $source));
    }

    public function valueExamples(): array
    {
        $name = function (int $length) { return str_repeat('x', $length); };

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

    private function assertToken(string $value, Token $token): void
    {
        $this->assertEquals(new Token\BasicToken('foo', $value), $token);
    }

    private function config(array $remotes = []): string
    {
        $remoteConfig = '';
        foreach ($remotes as $name => $uri) {
            $remoteConfig .= <<<INI
                [remote "$name"]
                    url = $uri
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
