<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token\Source;

use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Environment\Input;
use Shudd3r\PackageFiles\Application\Token\Parser;


class InteractiveInput implements Source
{
    private string $prompt;
    private Input  $input;
    private Source $source;

    public function __construct(string $prompt, Input $input, Source $source)
    {
        $this->prompt = $prompt;
        $this->input  = $input;
        $this->source = $source;
    }

    public function value(Parser $parser): string
    {
        $defaultValue  = $this->source->value($parser);
        $promptPostfix = $defaultValue ? ' [default: `' . $defaultValue . '`]:' : ':';

        return $this->input->value($this->prompt . $promptPostfix) ?: $defaultValue;
    }
}
