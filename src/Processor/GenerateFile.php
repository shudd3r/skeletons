<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Processor;

use Shudd3r\PackageFiles\Processor;
use Shudd3r\PackageFiles\Application\FileSystem\File;
use Shudd3r\PackageFiles\Token;
use Shudd3r\PackageFiles\Template;


class GenerateFile implements Processor
{
    private Template $template;
    private File     $file;

    public function __construct(Template $template, File $file)
    {
        $this->template = $template;
        $this->file     = $file;
    }

    public function process(Token $token): void
    {
        $contents = $this->template->render($token);
        $this->file->write($contents);
    }
}
