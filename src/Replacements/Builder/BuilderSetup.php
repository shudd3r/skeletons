<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Replacements\Builder;

use Closure;


class BuilderSetup
{
    private ReplacementBuilder $builder;

    public function __construct(ReplacementBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function token(Closure $token): self
    {
        $this->builder->token($token);
        return $this;
    }

    public function validate(Closure $validate): self
    {
        $this->builder->validate($validate);
        return $this;
    }

    public function inputPrompt(string $inputPrompt): self
    {
        $this->builder->inputPrompt($inputPrompt);
        return $this;
    }

    public function optionName(string $optionName): self
    {
        $this->builder->optionName($optionName);
        return $this;
    }

    public function description(string $description): self
    {
        $this->builder->description($description);
        return $this;
    }
}
