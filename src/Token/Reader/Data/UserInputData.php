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

    public function value(string $prompt, ?string $optionName = null, callable $default = null): string
    {
        if (!$value = $this->commandLineOption($optionName)) {
            $value = isset($default) ? $default() : '';
        }

        if (!$this->isInteractive()) { return $value; }

        $defaultInfo = $value ? ' [default: ' . $value . ']:' : ':';
        return $this->input->value($prompt . $defaultInfo) ?: $value;
    }

    private function commandLineOption(?string $name): ?string
    {
        if ($name === null) { return null; }
        return $this->options[$name] ?? null;
    }

    private function isInteractive(): bool
    {
        return isset($this->options['i']) || isset($this->options['interactive']);
    }
}
