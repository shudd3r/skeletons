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
use Shudd3r\PackageFiles\Application\Template\Templates;
use Shudd3r\PackageFiles\Application\Token\TokenCache;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Application\Token;


class FilesValidator extends Processor\FilesProcessor
{
    private ?TokenCache $cache;

    public function __construct(Directory $generatedFiles, Templates $templates, TokenCache $cache = null)
    {
        $this->cache = $cache;
        parent::__construct($generatedFiles, $templates);
    }

    protected function processor(Template $template, File $packageFile): Processor
    {
        $compareFiles  = new Processor\CompareFile($template, $packageFile);
        $contentsToken = new Token\CompositeToken(
            new Token\InitialContents(false),
            $this->originalContentsToken($packageFile)
        );

        return new Processor\ExpandedTokenProcessor($contentsToken, $compareFiles);
    }

    protected function originalContentsToken(File $packageFile): Token
    {
        return $this->cache
            ? new Token\CachedOriginalContents($packageFile->contents(), $packageFile->name(), $this->cache)
            : new Token\OriginalContents($packageFile->contents());
    }
}
