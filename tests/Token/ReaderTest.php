<?php declare(strict_types=1);

namespace Shudd3r\PackageFiles\Tests\Token;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Token\Reader\CompositeReader;
use Shudd3r\PackageFiles\Token;
use Shudd3r\PackageFiles\Tests\Doubles;
use Exception;


class ReaderTest extends TestCase
{
    public function testPropertiesAreBuiltWithSourceData()
    {
        $source = new Doubles\FakeSource();
        $reader = new CompositeReader($source, new Doubles\MockedTerminal());

        $expected = new Token\TokenGroup(
            new Token\Repository($source->repositoryName()),
            new Token\Package($source->packageName()),
            new Token\Description($source->packageDescription()),
            new Token\MainNamespace($source->sourceNamespace())
        );

        $this->assertEquals($expected, $reader->token());
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

        $this->assertInvalid($source, 3);
    }

    private function assertInvalid(Token\Source $source, int $errorMessages = 1): void
    {
        $output = new Doubles\MockedTerminal();
        $reader = new CompositeReader($source, $output);

        $this->expectException(Exception::class);
        $reader->token();

        $this->assertNotEquals(0, $output->exitCode());
        $this->assertSame($errorMessages, count($output->messagesSent));
    }

    private function assertValid(Token\Source $source): void
    {
        $output = new Doubles\MockedTerminal();
        $reader = new CompositeReader($source, $output);

        $this->assertInstanceOf(Token::class, $reader->token());
        $this->assertEquals(0, $output->exitCode());
    }
}
