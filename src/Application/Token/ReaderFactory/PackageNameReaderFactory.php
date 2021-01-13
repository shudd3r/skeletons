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


class PackageNameReaderFactory extends ValueReaderFactory
{
    protected ?string $inputPrompt = 'Packagist package name';
    protected ?string $optionName  = 'package';

    public function isValid(string $value): bool
    {
        return (bool) preg_match('#^[a-z0-9](?:[_.-]?[a-z0-9]+)*/[a-z0-9](?:[_.-]?[a-z0-9]+)*$#iD', $value);
    }

    protected function defaultSource(): Source
    {
        $composer = new Source\Data\ComposerJsonData($this->env->package()->file('composer.json'));
        return $this->userSource(new Source\DefaultPackageName($composer, $this->env->package()));
    }

    protected function newReaderInstance(Source $source): Reader
    {
        return new Reader\PackageName($source);
    }
}
