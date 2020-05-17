<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Command\Subroutine;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Command\Subroutine\ValidateProperties;
use Shudd3r\PackageFiles\Tests\Doubles\FakeProperties;
use Shudd3r\PackageFiles\Tests\Doubles\MockedSubroutine;
use Shudd3r\PackageFiles\Tests\Doubles\MockedTerminal;


class ValidatePropertiesTest extends TestCase
{
    private MockedTerminal   $output;
    private MockedSubroutine $forwarded;

    /**
     * @dataProvider githubUrls
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testRepoUrlValidation(string $invalid, string $valid)
    {
        $properties = new FakeProperties(['repositoryUrl' => $invalid]);
        $this->subroutine()->process($properties);
        $this->assertNull($this->forwarded->passedProperties, 'expected invalid name');
        $this->assertSame(1, $this->output->exitCode());
        $this->assertSame("Invalid github uri `{$invalid}`", $this->output->messagesSent[0]);

        $properties = new FakeProperties(['repositoryUrl' => $valid]);
        $this->subroutine()->process($properties);
        $this->assertSame($properties, $this->forwarded->passedProperties, 'expected valid name');
        $this->assertSame(0, $this->output->exitCode());
    }

    public function githubUrls()
    {
        return [
            ['http://github.com/repo/name.git', 'https://github.com/repo/name.git'],
            ['https://github.com/Repo/Name', 'https://github.com/Repo/Name.git'],
            ['https://github.com/repo/na(me).git', 'https://github.com/repo/na-me.git'],
            ['git@github.com:-repo/name.git', 'git@github.com:repo/name.git'],
            ['git@github.com:repo_/name.git', 'git@github.com:repo/name.git'],
            ['git@github.com:re--po/name.git', 'git@github.com:re-po/name.git'],
            [
                'git@github.com:40charactersUser789012345678901234567890/name.git',
                'git@github.com:39charactersUser78901234567890123456789/name.git'
            ],
            [
                'https://github.com/user/101charsName34567890123456789012345678901234567890123456789012345678901234567890123456789012345678901.git',
                'https://github.com/user/100charsName3456789012345678901234567890123456789012345678901234567890123456789012345678901234567890.git'
            ]
        ];
    }

    private function subroutine(): ValidateProperties
    {
        $this->output    = new MockedTerminal();
        $this->forwarded = new MockedSubroutine();

        return new ValidateProperties($this->output, $this->forwarded);
    }
}
