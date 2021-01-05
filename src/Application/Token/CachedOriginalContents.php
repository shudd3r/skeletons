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

use Shudd3r\PackageFiles\Application\Token;
use Shudd3r\PackageFiles\Environment\FileSystem\File;


class CachedOriginalContents extends OriginalContents
{
    private TokenCache $cache;

    public function __construct(File $packageFile, TokenCache $cache)
    {
        $this->cache = $cache;
        parent::__construct($packageFile);
    }

    protected function newTokenInstance(array $values = null): Token
    {
        $token = parent::newTokenInstance($values);
        $this->cache->add($this->packageFile->name(), $token);

        return $token;
    }
}
