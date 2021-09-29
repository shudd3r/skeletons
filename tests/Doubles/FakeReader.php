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

use Shudd3r\PackageFiles\Application\Token\Reader;
use Shudd3r\PackageFiles\Application\Token;
use Shudd3r\PackageFiles\Replacement;


class FakeReader extends Reader
{
    private bool $returnsToken;

    public function __construct(bool $returnsToken = true)
    {
        $this->returnsToken = $returnsToken;
        parent::__construct([]);
    }

    public function token(string $namespace = ''): ?Token
    {
        return $this->returnsToken ? new Token\ValueToken('placeholder', 'foo') : null;
    }

    public function value(): array
    {
        return $this->returnsToken ? ['placeholder' => 'foo'] : [];
    }

    protected function tokenInstance(string $name, Replacement $replacement): ?Token
    {
        return null;
    }
}
