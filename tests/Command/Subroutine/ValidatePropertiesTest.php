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

    public function testRepoUrlValidation()
    {
        $githubUrls = [
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

        foreach ($githubUrls as $urls) {
            $this->assertInvalid($urls[0], 'repositoryUrl', 'Invalid github uri');
            $this->assertValid($urls[1], 'repositoryUrl');
        }
    }

    public function testPackageNameValidation()
    {
        $packageNames = [
            ['-Packa-ge1/na.me', 'Packa-ge1/na.me'],
            ['1Package000_/na_Me', '1Package000/na_Me']
        ];

        foreach ($packageNames as $packageName) {
            $this->assertInvalid($packageName[0], 'packageName', 'Invalid packagist package name');
            $this->assertValid($packageName[1], 'packageName');
        }
    }

    private function assertValid(string $valid, string $propertyName): void
    {
        $properties = new FakeProperties([$propertyName => $valid]);
        $this->subroutine()->process($properties);
        $this->assertSame($properties, $this->forwarded->passedProperties, 'expected valid name');
        $this->assertSame(0, $this->output->exitCode());
    }

    private function assertInvalid(string $invalid, string $propertyName, string $errorMessage): void
    {
        $properties = new FakeProperties([$propertyName => $invalid]);
        $this->subroutine()->process($properties);
        $this->assertNull($this->forwarded->passedProperties, 'expected invalid name');
        $this->assertSame(1, $this->output->exitCode());
        $this->assertSame("$errorMessage `{$invalid}`", $this->output->messagesSent[0]);
    }

    private function subroutine(): ValidateProperties
    {
        $this->output    = new MockedTerminal();
        $this->forwarded = new MockedSubroutine();

        return new ValidateProperties($this->output, $this->forwarded);
    }
}
