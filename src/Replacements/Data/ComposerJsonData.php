<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Replacements\Data;

use Shudd3r\Skeletons\Environment\FileSystem\File;
use RuntimeException;


class ComposerJsonData
{
    private File  $composer;
    private array $data;

    public function __construct(File $composer)
    {
        $this->composer = $composer;
    }

    public function value(string $path): ?string
    {
        isset($this->data) or $this->data = $this->generateData();
        return $this->readValue(explode('.', $path), fn($value) => is_string($value));
    }

    public function array(string $path): ?array
    {
        isset($this->data) or $this->data = $this->generateData();
        return $this->readValue(explode('.', $path), fn($value) => is_array($value));
    }

    private function readValue(array $keys, callable $validType)
    {
        $value = &$this->data;
        while ($key = array_shift($keys)) {
            if (!is_array($value)) {
                throw new RuntimeException('Invalid composer data query');
            }
            if (!isset($value[$key])) { return null; }
            $value = &$value[$key];
        }

        if (!$validType($value)) {
            throw new RuntimeException('Invalid composer data type requested');
        }

        return $value;
    }

    private function generateData(): array
    {
        $composerData = $this->composer->exists() ? json_decode($this->composer->contents(), true) : [];
        if (!is_array($composerData)) {
            throw new RuntimeException('Invalid composer.json file');
        }

        return $composerData;
    }
}
