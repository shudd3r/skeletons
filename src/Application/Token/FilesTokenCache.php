<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token;

use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Application\Token;


class FilesTokenCache
{
    private array $tokens;

    public function __construct(array $tokens = [])
    {
        $this->tokens = $tokens;
    }

    public function add(File $file, Token $token): void
    {
        $this->tokens[$file->name()] = $token;
    }

    public function token(File $file): Token
    {
        return $this->tokens[$file->name()];
    }
}
