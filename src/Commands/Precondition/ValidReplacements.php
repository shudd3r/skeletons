<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Commands\Precondition;

use Shudd3r\Skeletons\Commands\Precondition;
use Shudd3r\Skeletons\Replacements\Reader;
use Shudd3r\Skeletons\Environment\Output;


class ValidReplacements implements Precondition
{
    private Reader  $reader;
    private ?Output $output;

    public function __construct(Reader $reader, ?Output $output = null)
    {
        $this->reader = $reader;
        $this->output = $output;
    }

    public function isFulfilled(): bool
    {
        if ($this->reader->token()) { return true; }
        if (!$this->output) { return false; }

        $this->output->send(PHP_EOL);
        foreach ($this->reader->tokenValues() as $name => $value) {
            if ($value !== null) { continue; }
            $this->output->send('    x Invalid value for {' . $name . '}' . PHP_EOL);
        }

        return false;
    }
}
