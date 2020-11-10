<?php

namespace Shudd3r\PackageFiles\Tests\Token\Reader;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Token;
use Shudd3r\PackageFiles\Tests\Doubles;
use Exception;


class DescriptionReaderTest extends TestCase
{
    /**
     * @dataProvider valueExamples
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testInvalidReaderValue_ThrowsException(string $invalid, string $valid)
    {
        $reader = $this->reader($valid);
        $this->assertInstanceOf(Token::class, $reader->token());
        $reader = $this->reader($invalid);
        $this->expectException(Exception::class);
        $reader->token();
    }

    public function valueExamples()
    {
        return [['', 'package description']];
    }

    protected function reader(string $value): Token\Reader
    {
        return new Token\Reader\DescriptionReader(new Doubles\FakeSource($value));
    }
}
