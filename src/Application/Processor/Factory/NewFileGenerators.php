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
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Application\Token;


class NewFileGenerators extends FileGenerators
{
    protected function fileGenerator(Template $template, File $packageFile): Processor
    {
        $processor = new Processor\GenerateFile($template, $packageFile);
        $token     = $this->initialContentToken();
        return new Processor\ExpandedTokenProcessor($token, $processor);
    }

    private function initialContentToken(): Token
    {
        return new Token\CompositeToken(
            new Token\ValueToken(Token\OriginalContents::PLACEHOLDER, ''),
            new Token\InitialContents()
        );
    }
}
