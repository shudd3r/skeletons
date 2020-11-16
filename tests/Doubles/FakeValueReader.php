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
    public FakeSource     $fakeSource;

    public function __construct(?string $value)
    {
        $this->output     = new MockedTerminal();
        $this->fakeSource = new FakeSource($value);

        parent::__construct($this->fakeSource, $this->output);
    }

    public function create(string $value): Token
    {
        $token = $this->source->create($value);
        if (!$token) {
            throw new Exception('exception message');
        }
        return $token;
    }
}
