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
use Shudd3r\PackageFiles\Token;


class DescriptionReader implements Reader
{
    private UserInputData    $input;
    private ComposerJsonData $composer;
    private Source           $fallback;

    public function __construct(UserInputData $input, ComposerJsonData $composer, Source $fallback)
    {
        $this->input    = $input;
        $this->composer = $composer;
        $this->fallback = $fallback;
    }

    public function token(): Token
    {
        $value = $this->input->value('Package description', 'desc', fn() => $this->readSource());
        return new Token\Description($value);
    }

    private function readSource(): string
    {
        return $this->composer->value('description') ?? $this->fallback->value() . ' package';
    }
}
