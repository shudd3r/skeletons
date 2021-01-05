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
use Shudd3r\PackageFiles\Application\Token\TokenCache;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Application\Token;


class UpdatedFileGenerators extends FileGenerators
{
    private TokenCache $cache;

    public function __construct(Directory $package, TokenCache $cache)
    {
        $this->cache = $cache;
        parent::__construct($package);
    }

    protected function fileGenerator(Template $template, File $packageFile): Processor
    {
        $processor = new Processor\GenerateFile($template, $packageFile);
        $token     = $this->cache->token($packageFile->name());

        return $token ? $this->originalContentProcessor($token, $processor) : $processor;
    }

    private function originalContentProcessor(Token $token, Processor $processor): Processor
    {
        $originalContents = new Token\CompositeToken(new Token\InitialContents(false), $token);
        return new Processor\ExpandedTokenProcessor($originalContents, $processor);
    }
}
