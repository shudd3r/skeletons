<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Replacement;

use Shudd3r\PackageFiles\Replacement;
use Shudd3r\PackageFiles\Application\Token\ValueToken;
use Shudd3r\PackageFiles\Application\Token\CompositeValueToken;
use Shudd3r\PackageFiles\Application\RuntimeEnv;


class PackageName implements Replacement
{
    public function optionName(): ?string
    {
        return 'package';
    }

    public function inputPrompt(): ?string
    {
        return 'Packagist package name';
    }

    public function token(string $name, string $value): ?ValueToken
    {
        if (!$this->isValid($value)) { return null; }

        $subToken = new ValueToken($name . '.title', $this->titleName($value));
        return new CompositeValueToken($name, $value, $subToken);
    }

    public function isValid(string $value): bool
    {
        return (bool) preg_match('#^[a-z0-9](?:[_.-]?[a-z0-9]+)*/[a-z0-9](?:[_.-]?[a-z0-9]+)*$#iD', $value);
    }

    public function defaultValue(RuntimeEnv $env, array $options): string
    {
        return $env->composer()->value('name') ?? $this->directoryFallback($env);
    }

    private function directoryFallback(RuntimeEnv $env): string
    {
        $path = $env->package()->path();
        return $path ? basename(dirname($path)) . '/' . basename($path) : '';
    }

    private function titleName(string $value): string
    {
        [$vendor, $package] = explode('/', $value);
        return ucfirst($vendor) . '/' . ucfirst($package);
    }
}
