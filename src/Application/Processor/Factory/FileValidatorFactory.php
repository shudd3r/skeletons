<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Processor\Factory;

use Shudd3r\PackageFiles\Application\Processor;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Application\Token\TokenCache;
use Shudd3r\PackageFiles\Application\Token\OriginalContents;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Application\Template;


class FileValidatorFactory implements Processor\Factory
{
    private Directory   $package;
    private ?TokenCache $cache;

    public function __construct(Directory $package, ?TokenCache $cache)
    {
        $this->package = $package;
        $this->cache   = $cache;
    }

    public function processor(File $skeletonFile): Processor
    {
        $template    = new Template\FileTemplate($skeletonFile);
        $packageFile = $this->package->file($skeletonFile->name());

        $compareFiles     = new Processor\CompareFile($template, $packageFile);
        $originalContents = new OriginalContents($packageFile, $this->cache);
        return new Processor\ExpandedTokenProcessor($originalContents, $compareFiles);
    }
}
