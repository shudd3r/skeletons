<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Template;

use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Application\Token;
use InvalidArgumentException;


class FileTemplate implements Template
{
    private File $templateFile;

    public function __construct(File $templateFile)
    {
        if (!$templateFile->exists()) {
            throw new InvalidArgumentException();
        }
        $this->templateFile = $templateFile;
    }

    public function render(Token $token): string
    {
        return $token->replacePlaceholders($this->templateFile->contents());
    }
}
