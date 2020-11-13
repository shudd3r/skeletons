<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Doubles;

use Shudd3r\PackageFiles\Token\Reader\ValueReader;
use Shudd3r\PackageFiles\Token;
use Exception;


class FakeValueReader extends ValueReader
{
    public MockedTerminal $output;
    public FakeSource     $source;

    private bool $valid;

    public function __construct(?string $value)
    {
        $this->output = new MockedTerminal();
        $this->source = new FakeSource($value ?? '');
        $this->valid  = isset($value);
        parent::__construct($this->source, $this->output);
    }

    protected function createToken(string $value): Token
    {
        if (!$this->valid) {
            throw new Exception('exception message');
        }
        return new FakeToken($value);
    }
}
