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


class FakeReader extends Reader
{
    private bool $returnsTokens;

    public function __construct(bool $returnsTokens = true)
    {
        $this->returnsTokens = $returnsTokens;
        parent::__construct(new Token\Replacements([]));
    }

    protected function tokens(): array
    {
        return ['placeholder' => $this->returnsTokens ? new Token\ValueToken('placeholder', 'foo') : null];
    }
}
