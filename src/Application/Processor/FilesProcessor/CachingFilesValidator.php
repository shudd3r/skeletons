<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Processor\FilesProcessor;

use Shudd3r\PackageFiles\Application\Token\TokenCache;
use Shudd3r\PackageFiles\Application\Template\Templates;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Application\Token;


class CachingFilesValidator extends FilesValidator
{
    private TokenCache $cache;

    public function __construct(Directory $generatedFiles, Templates $templates, TokenCache $cache)
    {
        $this->cache = $cache;
        parent::__construct($generatedFiles, $templates);
    }

    protected function originalContentsToken(File $packageFile): Token
    {
        return new Token\CachedOriginalContents($packageFile->contents(), $packageFile->name(), $this->cache);
    }
}
