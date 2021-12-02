<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Setup\Builder;

use Closure;


class BuildSetup
{
    private ReplacementBuilder $builder;

    public function __construct(ReplacementBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param Closure $validate fn (string $value) => bool
     */
    public function validate(Closure $validate): self
    {
        $this->builder->validate($validate);
        return $this;
    }

    /**
     * @param Closure $createToken fn (string $placeholder, string $value) => Token
     */
    public function token(Closure $createToken): self
    {
        $this->builder->token($createToken);
        return $this;
    }

    public function inputPrompt(string $inputPrompt): self
    {
        $this->builder->inputPrompt($inputPrompt);
        return $this;
    }

    public function argumentName(string $argumentName): self
    {
        $this->builder->argumentName($argumentName);
        return $this;
    }

    public function description(string $description): self
    {
        $this->builder->description($description);
        return $this;
    }
}
