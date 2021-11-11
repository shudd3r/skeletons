<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons;


class InputArgs
{
    private string $script;
    private string $command;
    private array  $options   = [];
    private array  $arguments = [];

    public function __construct(array $argv)
    {
        $argv = array_values($argv) + ['skeleton-script', 'help'];
        $this->script  = basename(array_shift($argv));
        $this->command = array_shift($argv);
        $this->parseArgs($argv);
    }

    public function script(): string
    {
        return $this->script;
    }

    public function command(): string
    {
        return $this->command;
    }

    public function interactive(): bool
    {
        if (!in_array($this->command, ['init', 'update'])) { return false; }
        return !$this->arguments || isset($this->options['i']) || isset($this->options['interactive']);
    }

    public function remoteOnly(): bool
    {
        return isset($this->options['r']) || isset($this->options['remote']);
    }

    public function valueOf(string $name): string
    {
        return $this->arguments[$name] ?? '';
    }

    private function parseArgs(array $argv): void
    {
        foreach ($argv as $option) {
            [$name, $value] = explode('=', $option, 2) + ['', ''];
            $name[0] === '-' ? $this->addOption($name) : $this->arguments[$name] = $value;
        }
    }

    private function addOption(string $rawName): void
    {
        $name = ltrim($rawName, '-');
        if (!$name) { return; }

        if ($rawName[1] === '-') {
            $this->options[$name] = true;
            return;
        }

        $options = str_split($name);
        array_walk($options, fn (string $name) => $this->options[$name] = true);
    }
}
