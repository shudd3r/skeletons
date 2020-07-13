<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Token;

use Shudd3r\PackageFiles\Token;
use Exception;


class Package implements Token
{
    public const NAME  = '{package.name}';
    public const TITLE = '{package.title}';

    private string $name;

    public function __construct(string $name)
    {
        if (!preg_match('#^[a-z0-9](?:[_.-]?[a-z0-9]+)*/[a-z0-9](?:[_.-]?[a-z0-9]+)*$#iD', $name)) {
            throw new Exception("Invalid packagist package name `{$name}`");
        }

        $this->name = $name;
    }

    public function replacePlaceholders(string $template): string
    {
        $template = str_replace(self::NAME, $this->name, $template);
        return str_replace(self::TITLE, $this->titleName(), $template);
    }

    private function titleName(): string
    {
        [$vendor, $package] = explode('/', $this->name);
        return ucfirst($vendor) . '/' . ucfirst($package);
    }
}
