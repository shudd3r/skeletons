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
    public function __construct(bool $returnsTokens = true)
    {
        parent::__construct(new Token\Replacements([]), new FakeRuntimeEnv(), []);
        $this->tokens['placeholder'] = $returnsTokens ? new Token\ValueToken('placeholder', 'foo') : null;
    }

    public function readToken(string $name, Replacement $replacement): void
    {
    }
}
