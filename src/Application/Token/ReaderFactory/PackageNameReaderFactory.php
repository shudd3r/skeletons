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

use Shudd3r\PackageFiles\Application\Token\Reader;
use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Application\Token;


class PackageNameReaderFactory extends ValueReaderFactory
{
    protected ?string $inputPrompt = 'Packagist package name';
    protected ?string $optionName  = 'package';

    public function token(string $name, string $value): ?Token
    {
        $isValid = (bool) preg_match('#^[a-z0-9](?:[_.-]?[a-z0-9]+)*/[a-z0-9](?:[_.-]?[a-z0-9]+)*$#iD', $value);
        if (!$isValid) { return null; }

        return new Token\CompositeToken(
            new Token\ValueToken($name, $value),
            new Token\ValueToken($name . '.title', $this->titleName($value))
        );
    }

    protected function defaultSource(): Source
    {
        $callback = fn() => $this->env->composer()->value('name') ?? $this->directoryFallback($this->env->package());
        return $this->userSource(new Source\CallbackSource($callback));
    }

    protected function newReaderInstance(Source $source): Reader
    {
        return new Reader\PackageName($this, $source);
    }

    private function directoryFallback(Directory $rootDirectory): string
    {
        $path = $rootDirectory->path();
        return $path ? basename(dirname($path)) . '/' . basename($path) : '';
    }

    private function titleName(string $value): string
    {
        [$vendor, $package] = explode('/', $value);
        return ucfirst($vendor) . '/' . ucfirst($package);
    }
}
