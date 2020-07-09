<?php

namespace Shudd3r\PackageFiles\Tests\Properties;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Properties\Reader;
use Shudd3r\PackageFiles\Properties;
use Shudd3r\PackageFiles\Tests\Doubles;


class ReaderTest extends TestCase
{
    public function testPropertiesAreBuiltWithSourceData()
    {
        $source = new Doubles\FakeSource();
        $reader = new Reader($source, new Doubles\MockedTerminal());

        $expected = new Properties(
            $source->repositoryName(),
            $source->packageName(),
            $source->packageDescription(),
            $source->sourceNamespace()
        );

        $this->assertEquals($expected, $reader->properties());
    }

    public function testRepositoryNameValidation()
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
            $this->assertInvalid(new Doubles\FakeSource(['repositoryName' => $invalid]));
            $this->assertValid(new Doubles\FakeSource(['repositoryName' => $valid]));
        }
    }

    public function testPackageNameValidation()
    {
        $packageNames = [
            '-Packa-ge1/na.me'   => 'Packa-ge1/na.me',
            '1Package000_/na_Me' => '1Package000/na_Me'
        ];

        foreach ($packageNames as $invalid => $valid) {
            $this->assertInvalid(new Doubles\FakeSource(['packageName' => $invalid]));
            $this->assertValid(new Doubles\FakeSource(['packageName' => $valid]));
        }
    }

    public function testPackageDescriptionValidation()
    {
        $this->assertInvalid(new Doubles\FakeSource(['packageDescription' => '']));
        $this->assertValid(new Doubles\FakeSource(['packageDescription' => 'Some package description']));
    }

    public function testSrcNamespaceValidation()
    {
        $namespaces = [
            'Foo/Bar'           => 'Foo\Bar',
            '_Foo\1Bar\Baz'     => '_Foo\_1Bar\Baz',
            'Package:000\na_Me' => 'Package000\na_Me'
        ];

        foreach ($namespaces as $invalid => $valid) {
            $this->assertInvalid(new Doubles\FakeSource(['sourceNamespace' => $invalid]));
            $this->assertValid(new Doubles\FakeSource(['sourceNamespace' => $valid]));
        }
    }

    public function testMultipleValidationErrors()
    {
        $source = new Doubles\FakeSource([
            'repositoryName'     => 'repo',
            'packageName'        => '1Package000_/na_Me',
            'packageDescription' => '',
            'sourceNamespace'    => '_Foo\1Bar\Baz'
        ]);

        $this->assertInvalid($source, 4);
    }

    private function assertInvalid(Properties\Source $source, int $errorMessages = 1): void
    {
        $output = new Doubles\MockedTerminal();
        $reader = new Reader($source, $output);

        $this->assertNull($reader->properties());
        $this->assertNotEquals(0, $output->exitCode());
        $this->assertSame($errorMessages, count($output->messagesSent));
    }

    private function assertValid(Properties\Source $source): void
    {
        $output = new Doubles\MockedTerminal();
        $reader = new Reader($source, $output);

        $this->assertInstanceOf(Properties::class, $reader->properties());
        $this->assertEquals(0, $output->exitCode());
    }
}
