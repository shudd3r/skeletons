<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Token\Reader\Source;

use Shudd3r\PackageFiles\Application\Input;
use Shudd3r\PackageFiles\Token\Reader\Source;


class InteractiveInput implements Source
{
    private string  $prompt;
    private Input   $input;
    private ?Source $default;

    public function __construct(string $prompt, Input $input, Source $default = null)
    {
        $this->prompt  = $prompt;
        $this->input   = $input;
        $this->default = $default;
    }

    public function value(): string
    {
        if (!$this->default) {
            return $this->input->value($this->prompt . ':');
        }

        $default = $this->default->value();
        return $this->input->value($this->prompt . ' [default: ' . $default . ']:') ?: $default;
    }
}
