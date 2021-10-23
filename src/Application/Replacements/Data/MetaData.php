<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Replacements\Data;

use Shudd3r\PackageFiles\Environment\FileSystem\File;


class MetaData
{
    private File  $metaFile;
    private array $data;

    public function __construct(File $metaFile)
    {
        $this->metaFile = $metaFile;
    }

    public function value(string $name): ?string
    {
        $this->data ??= $this->generateData();
        return $this->data[$name] ?? null;
    }

    public function save(array $data): void
    {
        $contents = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
        $this->metaFile->write($contents);
        $this->data = $data;
    }

    private function generateData(): array
    {
        $data = json_decode($this->metaFile->contents(), true);
        return is_array($data) ? $data : [];
    }
}
