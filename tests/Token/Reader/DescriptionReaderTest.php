<?php

namespace Shudd3r\PackageFiles\Tests\Token\Reader;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Token;
use Shudd3r\PackageFiles\Tests\Doubles;


class DescriptionReaderTest extends TestCase
{
    /**
     * @dataProvider valueExamples
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testInvalidReaderValue_ReturnsNull(string $invalid, string $valid)
    {
        $this->assertInstanceOf(Token::class, $this->reader($valid)->token());
        $this->assertNull($this->reader($invalid)->token());
    }

    public function valueExamples()
    {
        return [['', 'package description']];
    }

    protected function reader(string $value): Token\Reader
    {
        return new Token\Reader\DescriptionReader(new Doubles\FakeSource($value), new Doubles\MockedTerminal());
    }
}
