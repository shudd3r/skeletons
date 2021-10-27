<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Processors;

use Shudd3r\PackageFiles\Processors;
use Shudd3r\PackageFiles\Templates\Template;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Replacements\TokenCache;
use Shudd3r\PackageFiles\Replacements\Token;


class FileGenerators implements Processors
{
    private ?TokenCache $cache;

    public function __construct(?TokenCache $cache = null)
    {
        $this->cache = $cache;
    }

    public function processor(Template $template, File $packageFile): Processor
    {
        $processor = new Processor\GenerateFile($template, $packageFile);
        $token     = $this->originalContentsToken($packageFile->name());
        return $token ? new Processor\ExpandedTokenProcessor($token, $processor) : $processor;
    }

    private function originalContentsToken(string $filename): ?Token
    {
        if (!$this->cache) {
            $originalContentsToken = new Token\ValueToken(Token\OriginalContents::PLACEHOLDER, '');
            return new Token\CompositeToken($originalContentsToken, new Token\InitialContents());
        }

        $token = $this->cache->token($filename);
        return $token ? new Token\CompositeToken(new Token\InitialContents(false), $token) : null;
    }
}
