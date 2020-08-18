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
use Shudd3r\PackageFiles\Token;


class DescriptionReader extends ValueReader
{
    private ComposerJsonData $composer;
    private ValueReader      $fallback;

    protected string $inputPrompt = 'Package description';
    protected string $optionName  = 'desc';

    public function __construct(UserInputData $input, ComposerJsonData $composer, ValueReader $fallback)
    {
        parent::__construct($input);
        $this->composer = $composer;
        $this->fallback = $fallback;
    }

    protected function createToken(string $value): Token
    {
        return new Token\Description($value);
    }

    protected function sourceValue(): string
    {
        return $this->composer->value('description') ?? $this->fallback->value() . ' package';
    }
}
