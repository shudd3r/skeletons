<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Doubles;

use Shudd3r\Skeletons\Replacements;
use Shudd3r\Skeletons\InputArgs;
use Shudd3r\Skeletons\Replacements\Reader;
use Shudd3r\Skeletons\Replacements\Replacement;
use Shudd3r\Skeletons\Replacements\Token;


class FakeReader extends Reader
{
    public function __construct(bool $returnsTokens = true)
    {
        parent::__construct(new Replacements([]), new FakeRuntimeEnv(), new InputArgs([]));
        $this->tokens['placeholder'] = $returnsTokens ? new Token\ValueToken('placeholder', 'foo') : null;
    }

    public function readToken(string $name, Replacement $replacement): bool
    {
        return true;
    }
}
