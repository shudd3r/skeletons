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
    private const SHORT_OPTIONS = [
        'i' => 'interactive',
        'l' => 'local'
    ];

    private string $script;
    private string $command;

    private array $options = [
        'interactive' => false,
        'local'       => false
    ];

    private array $arguments = [];

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
        return in_array($this->command, ['init', 'update']) && (!$this->arguments || $this->options['interactive']);
    }

    public function includeLocalFiles(): bool
    {
        return $this->options['local'];
    }

    public function valueOf(string $name): string
    {
        return $this->arguments[$name] ?? '';
    }

    private function parseArgs(array $argv): void
    {
        foreach ($argv as $option) {
            [$name, $value] = explode('=', $option, 2) + ['', ''];
            $name[0] === '-' ? $this->parseOption($name) : $this->arguments[$name] = $value;
        }
    }

    private function parseOption(string $rawName): void
    {
        $name = ltrim($rawName, '-');
        if (!$name) { return; }

        if ($rawName[1] === '-') {
            $this->setOption($name);
            return;
        }

        $options = str_split($name);
        array_walk($options, fn (string $name) => $this->setOption(self::SHORT_OPTIONS[$name] ?? '-'));
    }

    private function setOption(string $name): void
    {
        if (!isset($this->options[$name])) { return; }
        $this->options[$name] = true;
    }
}
