<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Processors;

use Shudd3r\Skeletons\Processors;
use Shudd3r\Skeletons\Replacements\Token;
use Shudd3r\Skeletons\Templates\Template;
use Shudd3r\Skeletons\Environment\Files\File;
use Shudd3r\Skeletons\Replacements\TokenCache;
use Shudd3r\Skeletons\Environment\Output;


class FileValidators implements Processors
{
    private Output      $output;
    private ?TokenCache $cache;

    public function __construct(Output $output, ?TokenCache $cache = null)
    {
        $this->output = $output;
        $this->cache  = $cache;
    }

    public function processor(Template $template, File $packageFile): Processor
    {
        $compareFiles  = $this->processorInfo(new Processor\CompareFile($template, $packageFile), $packageFile->name());
        $contentsToken = new Token\CompositeToken(
            new Token\InitialContents(false),
            $this->originalContentsToken($packageFile)
        );

        return new Processor\ExpandedTokenProcessor($contentsToken, $compareFiles);
    }

    private function originalContentsToken(File $packageFile): Token
    {
        return $this->cache
            ? new Token\CachedOriginalContents($packageFile->contents(), $packageFile->name(), $this->cache)
            : new Token\OriginalContents($packageFile->contents());
    }

    private function processorInfo(Processor $processor, string $filename): Processor
    {
        return new Processors\Processor\DescribedProcessor($processor, $this->output, $filename, true);
    }
}
