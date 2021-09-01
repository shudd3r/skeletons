<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Fixtures;


class ExampleFiles
{
    private string $directory;

    public function __construct(string $directory)
    {
        $this->directory = __DIR__ . DIRECTORY_SEPARATOR . $directory;
    }

    public function contentsOf(string $filename): string
    {
        return file_get_contents($this->directory . DIRECTORY_SEPARATOR . $filename);
    }
}
