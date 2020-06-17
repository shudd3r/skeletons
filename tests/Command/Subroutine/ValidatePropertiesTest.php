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
use Shudd3r\PackageFiles\Tests\Doubles;


class ValidatePropertiesTest extends TestCase
{
    private Doubles\MockedTerminal   $output;
    private Doubles\MockedSubroutine $forwarded;

    public function testRepoNameValidation()
    {
        $name = function (int $length) { return str_pad('x', $length, 'x'); };

        $longAccount  = $name(40) . '/name';
        $shortAccount = $name(39) . '/name';
        $longRepo     = 'user/' . $name(101);
        $shortRepo    = 'user/' . $name(100);

        $githubUrls = [
            'repo/na(me)' => 'repo/na-me',
            '-repo/name'  => 'r-epo/name',
            'repo_/name'  => 'repo/name',
            're--po/name' => 're-po/name',
            $longAccount  => $shortAccount,
            $longRepo     => $shortRepo
        ];

        foreach ($githubUrls as $invalid => $valid) {
            $this->assertInvalid($invalid, 'repositoryName', 'Invalid github uri');
            $this->assertValid($valid, 'repositoryName');
        }
    }

    public function testPackageNameValidation()
    {
        $packageNames = [
            '-Packa-ge1/na.me'   => 'Packa-ge1/na.me',
            '1Package000_/na_Me' => '1Package000/na_Me'
        ];

        foreach ($packageNames as $invalid => $valid) {
            $this->assertInvalid($invalid, 'packageName', 'Invalid packagist package name');
            $this->assertValid($valid, 'packageName');
        }
    }

    public function testSrcNamespaceValidation()
    {
        $namespaces = [
            'Foo/Bar'           => 'Foo\Bar',
            '_Foo\1Bar\Baz'     => '_Foo\_1Bar\Baz',
            'Package:000\na_Me' => 'Package000\na_Me'
        ];

        foreach ($namespaces as $invalid => $valid) {
            $this->assertInvalid($invalid, 'sourceNamespace', 'Invalid namespace');
            $this->assertValid($valid, 'sourceNamespace');
        }
    }

    public function testPackageDescriptionValidation()
    {
        $this->assertInvalid('', 'packageDescription', 'Package description cannot be empty');
        $this->assertValid('Description', 'packageDescription');
    }

    private function assertValid(string $valid, string $propertyName): void
    {
        $properties = new Doubles\FakeProperties([$propertyName => $valid]);
        $this->subroutine()->process($properties);
        $this->assertSame($properties, $this->forwarded->passedProperties, 'expected valid name - ' . $valid);
        $this->assertSame(0, $this->output->exitCode());
    }

    private function assertInvalid(string $invalid, string $propertyName, string $errorMessage): void
    {
        $properties = new Doubles\FakeProperties([$propertyName => $invalid]);
        $this->subroutine()->process($properties);
        $this->assertNull($this->forwarded->passedProperties, 'expected invalid name - ' . $invalid);
        $this->assertSame(1, $this->output->exitCode());
        $this->assertSame($errorMessage, substr($this->output->messagesSent[0], 0, strlen($errorMessage)));
    }

    private function subroutine(): ValidateProperties
    {
        $this->output    = new Doubles\MockedTerminal();
        $this->forwarded = new Doubles\MockedSubroutine();

        return new ValidateProperties($this->output, $this->forwarded);
    }
}
