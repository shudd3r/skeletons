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

use Shudd3r\PackageFiles\Token\Reader\Data\UserInputData;
use Shudd3r\PackageFiles\Token\Reader\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Application\FileSystem\Directory;
use Shudd3r\PackageFiles\Token;


class PackageReader extends ValueReader
{
    private ComposerJsonData $composer;
    private Directory        $directory;

    protected string $inputPrompt = 'Packagist package name';
    protected string $optionName  = 'package';

    public function __construct(UserInputData $input, ComposerJsonData $composer, Directory $directory)
    {
        parent::__construct($input);
        $this->composer  = $composer;
        $this->directory = $directory;
    }

    protected function createToken(string $value): Token
    {
        return new Token\Package($value);
    }

    protected function sourceValue(): string
    {
        return $this->composer->value('name') ?? $this->directoryFallback();
    }

    private function directoryFallback(): string
    {
        $path = $this->directory->path();
        return $path ? basename(dirname($path)) . '/' . basename($path) : '';
    }
}
