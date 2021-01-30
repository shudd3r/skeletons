<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token\ReaderFactory;

use Shudd3r\PackageFiles\Application\Token\ValueToken;
use Shudd3r\PackageFiles\Application\Token\CompositeValueToken;
use Shudd3r\PackageFiles\Application\Token\Source;


class PackageNameReaderFactory extends ValueReaderFactory
{
    protected ?string $inputPrompt = 'Packagist package name';
    protected ?string $optionName  = 'package';

    public function token(string $name, string $value): ?ValueToken
    {
        $isValid = (bool) preg_match('#^[a-z0-9](?:[_.-]?[a-z0-9]+)*/[a-z0-9](?:[_.-]?[a-z0-9]+)*$#iD', $value);
        if (!$isValid) { return null; }

        $subToken = new ValueToken($name . '.title', $this->titleName($value));
        return new CompositeValueToken($name, $value, $subToken);
    }

    protected function defaultSource(): Source
    {
        $callback = fn() => $this->env->composer()->value('name') ?? $this->directoryFallback();
        return $this->userSource(new Source\CallbackSource($callback));
    }

    private function directoryFallback(): string
    {
        $path = $this->env->package()->path();
        return $path ? basename(dirname($path)) . '/' . basename($path) : '';
    }

    private function titleName(string $value): string
    {
        [$vendor, $package] = explode('/', $value);
        return ucfirst($vendor) . '/' . ucfirst($package);
    }
}
