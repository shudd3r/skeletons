<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Token\Reader;

use Shudd3r\PackageFiles\Token\Reader;
use Shudd3r\PackageFiles\Token\Reader\Data\UserInputData;
use Shudd3r\PackageFiles\Token\Reader\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Application\FileSystem\Directory;
use Shudd3r\PackageFiles\Token;


class PackageReader implements Reader, Source
{
    private UserInputData    $input;
    private ComposerJsonData $composer;
    private Directory        $directory;
    private ?string          $value = null;

    public function __construct(UserInputData $input, ComposerJsonData $composer, Directory $directory)
    {
        $this->input     = $input;
        $this->composer  = $composer;
        $this->directory = $directory;
    }

    public function token(): Token
    {
        return new Token\Package($this->value());
    }

    public function value(): string
    {
        if (isset($this->value)) { return $this->value; }

        $fallback = fn() => $this->readSource();
        return $this->value = $this->input->value('Packagist package name', 'package', $fallback);
    }

    private function readSource()
    {
        return $this->composer->value('name') ?? $this->directoryFallback();
    }

    private function directoryFallback(): string
    {
        $path = $this->directory->path();
        return $path ? basename(dirname($path)) . '/' . basename($path) : '';
    }
}
