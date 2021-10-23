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

use Shudd3r\PackageFiles\Application\Processor;
use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Application\Replacements\Token;
use Shudd3r\PackageFiles\Environment\FileSystem\File;


class FilesGenerator extends Processor\FilesProcessor
{
    protected function processor(Template $template, File $packageFile): Processor
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
