<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Setup;

use Shudd3r\Skeletons\Replacements\Replacement;
use Shudd3r\Skeletons\Replacements\Source;
use Shudd3r\Skeletons\Setup\Builder\ReplacementBuilder;
use Shudd3r\Skeletons\Setup\Builder\BuildSetup;
use Closure;


class ReplacementSetup
{
    private AppSetup $setup;
    private string   $placeholder;

    public function __construct(AppSetup $setup, string $placeholder)
    {
        $this->setup       = $setup;
        $this->placeholder = $placeholder;
    }

    public function add(Replacement $replacement): void
    {
        $this->setup->addReplacement($this->placeholder, $replacement);
    }

    /**
     * @param Closure $resolveValue fn (Source) => string
     *
     * @see Source
     */
    public function build(Closure $resolveValue): BuildSetup
    {
        $builder = new ReplacementBuilder($resolveValue);
        $this->setup->addBuilder($this->placeholder, $builder);
        return new BuildSetup($builder);
    }
}
