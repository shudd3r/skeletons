<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Processor\FileProcessors;

use Shudd3r\PackageFiles\Application\Token\TokenCache;
use Shudd3r\PackageFiles\Application\Template\Factory\Templates;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Application\Token;


class CachingFileValidators extends FileValidators
{
    private TokenCache $cache;

    public function __construct(Directory $package, Templates $templates, TokenCache $cache)
    {
        $this->cache = $cache;
        parent::__construct($package, $templates);
    }

    protected function originalContentsToken(File $packageFile): Token
    {
        return new Token\CachedOriginalContents($packageFile->contents(), $packageFile->name(), $this->cache);
    }
}