<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Token\Reader\Data;

use Shudd3r\PackageFiles\Application\Input;


class UserInputData
{
    private array $options;
    private Input $input;

    public function __construct(array $options, Input $input)
    {
        $this->options = $options;
        $this->input   = $input;
    }

    public function value(string $prompt, string $default): string
    {
        if (!$this->isInteractive()) { return $default; }
        return $this->input->value($prompt . ' [default: ' . $default . ']:') ?: $default;
    }

    public function commandLineOption(string $name): ?string
    {
        return $this->options[$name] ?? null;
    }

    private function isInteractive(): bool
    {
        return isset($this->options['i']) || isset($this->options['interactive']);
    }
}
