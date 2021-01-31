<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token\Source\Data;

use Shudd3r\PackageFiles\Environment\FileSystem\File;


class SavedPlaceholderValues
{
    private File  $metaFile;
    private array $data;

    public function __construct(File $metaFile)
    {
        $this->metaFile = $metaFile;
    }

    public function value(string $name): ?string
    {
        isset($this->data) or $this->data = $this->generateData();
        return $this->data[$name] ?? null;
    }

    private function generateData(): array
    {
        $data = json_decode($this->metaFile->contents(), true);
        return is_array($data) ? $data : [];
    }
}
