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
use Shudd3r\Skeletons\InputArgs;
use Shudd3r\Skeletons\RuntimeEnv;
use Shudd3r\Skeletons\Tests\Doubles;
use Closure;


class FakeReader extends Reader
{
    public function __construct(?RuntimeEnv $env = null, array $args = [])
    {
        parent::__construct($env ?? new Doubles\FakeRuntimeEnv(), new InputArgs($args));
    }

    public function commandArgument(string $argumentName): ?string
    {
        return $this->args->valueOf($argumentName);
    }

    public function inputString(string $prompt, Closure $isValid = null, int $tries = 0): ?string
    {
        if (!$this->args->interactive()) { return null; }
        $value = $this->env->input()->value($prompt);
        return !$isValid || $isValid($value) ? $value : '';
    }
}
