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

use Shudd3r\Skeletons\Replacements\Reader;
use Shudd3r\Skeletons\Replacements\Token\ValueToken;
use Shudd3r\Skeletons\Replacements;
use Shudd3r\Skeletons\InputArgs;


class FakeReader extends Reader
{
    private bool $returnsToken;

    public function __construct(bool $returnsTokens = true)
    {
        $this->returnsToken = $returnsTokens;
        parent::__construct(new FakeRuntimeEnv(), new InputArgs([]));
    }

    public function tokens(Replacements $replacements): array
    {
        return ['placeholder' => $this->readToken('placeholder')];
    }

    protected function readToken(string $name): ?ValueToken
    {
        return $this->returnsToken ? new ValueToken('placeholder', 'foo') : null;
    }
}
