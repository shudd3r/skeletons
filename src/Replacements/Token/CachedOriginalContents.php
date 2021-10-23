<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Replacements\Token;

use Shudd3r\PackageFiles\Replacements\Token;
use Shudd3r\PackageFiles\Replacements\TokenCache;


class CachedOriginalContents extends OriginalContents
{
    private TokenCache $cache;
    private string     $cacheKey;

    public function __construct(string $contents, string $cacheKey, TokenCache $cache)
    {
        $this->cache    = $cache;
        $this->cacheKey = $cacheKey;
        parent::__construct($contents);
    }

    protected function newTokenInstance(array $values = null): Token
    {
        $token = parent::newTokenInstance($values);
        $this->cache->add($this->cacheKey, $token);

        return $token;
    }
}
