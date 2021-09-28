<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Template\Factory;

use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Environment\FileSystem\File;


class Templates implements Template\Factory
{
    private array $factories;

    public function __construct(array $factories = [])
    {
        $this->factories = $factories;
    }

    public function add(string $filename, Template\Factory $factory): void
    {
        $this->factories[$filename] = $factory;
    }

    public function template(File $skeletonFile): Template
    {
        $factory = $this->factory($skeletonFile->name());
        return $factory ? $factory->template($skeletonFile) : new Template\BasicTemplate($skeletonFile->contents());
    }

    private function factory(string $filename): ?Template\Factory
    {
        return $this->factories[$filename] ?? null;
    }
}
