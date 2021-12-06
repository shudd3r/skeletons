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

use Shudd3r\Skeletons\InputArgs;
use Shudd3r\Skeletons\Replacements;
use Shudd3r\Skeletons\Replacements\Reader;
use Shudd3r\Skeletons\Replacements\Token;
use Shudd3r\Skeletons\Replacements\Tokens;


class FakeTokens extends Tokens
{
    private bool $returnsToken;

    public function __construct(bool $returnsToken = true)
    {
        $this->returnsToken = $returnsToken;
        parent::__construct(new Replacements([]), new Reader(new FakeRuntimeEnv(), new InputArgs([])));
    }

    public function compositeToken(): ?Token
    {
        return $this->returnsToken ? new Token\CompositeToken(new Token\BasicToken('foo', 'bar')) : null;
    }

    public function placeholderValues(): array
    {
        return $this->returnsToken ? ['foo' => 'bar'] : ['foo' => null];
    }
}
