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
use Shudd3r\PackageFiles\Token\Reader\Data;
use Shudd3r\PackageFiles\Token\Repository;
use Shudd3r\PackageFiles\Tests\Doubles;


class RepositoryReaderTest extends TestCase
{
    public function testTokensCreatedFromSingleSource()
    {
        $this->assertEquals(new Repository('fallback/repo'), $this->reader(false, false, false)->token());
        $this->assertEquals(new Repository('config/repo'), $this->reader(false, false, true)->token());
        $this->assertEquals(new Repository('option/repo'), $this->reader(false, true, false)->token());
        $this->assertEquals(new Repository('input/repo'), $this->reader(true, false, false)->token());
    }

    public function testTokensCreatedFromSourceWithHigherPriority()
    {
        $this->assertEquals(new Repository('option/repo'), $this->reader(false, true, true)->token());
        $this->assertEquals(new Repository('input/repo'), $this->reader(true, true, false)->token());
    }

    public function testConfigParsingPriority()
    {
        $reader = $this->configReader($config = []);
        $this->assertEquals(new Repository('fallback/repo'), $reader->token());

        $reader = $this->configReader($config += ['foo' => 'git@github.com:other/repo.git']);
        $this->assertEquals(new Repository('other/repo'), $reader->token());

        $reader = $this->configReader($config += ['origin' => 'https://github.com/orig/repo.git']);
        $this->assertEquals(new Repository('orig/repo'), $reader->token());

        $reader = $this->configReader($config + ['upstream' => 'git@github.com:master/ssh-repo.git']);
        $this->assertEquals(new Repository('master/ssh-repo'), $reader->token());
    }

    private function reader(bool $input, bool $options, bool $config): RepositoryReader
    {
        $config   = $config ? ['origin' => 'https://github.com/config/repo.git'] : [];
        $config   = new Doubles\MockedFile($this->config($config), (bool) $config);
        $options  = $options ? ['repo' => 'option/repo', 'i' => false] : ['i' => false];
        $input    = new Doubles\MockedTerminal($input ? ['input/repo'] : []);
        $input    = new Data\UserInputData($options, $input);
        $fallback = new Doubles\FakeValueReader($input, 'fallback/repo');

        return new RepositoryReader($input, $config, $fallback);
    }

    private function configReader(array $config = []): RepositoryReader
    {
        $input  = new Data\UserInputData([], new Doubles\MockedTerminal());
        $config = new Doubles\MockedFile($this->config($config));
        return new RepositoryReader($input, $config, new Doubles\FakeValueReader($input, 'fallback/repo'));
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
