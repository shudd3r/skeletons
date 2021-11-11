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
use Shudd3r\Skeletons\Replacements\ReplacementBuilder;
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
     * @see RuntimeEnv
     * @see FallbackReader
     *
     * @param Closure  $default fn (RuntimeEnv, FallbackReader) => string
     */
    public function build(Closure $default): ReplacementBuilder
    {
        $builder = new ReplacementBuilder($default);
        $this->setup->addBuilder($this->placeholder, $builder);
        return $builder;
    }
}
