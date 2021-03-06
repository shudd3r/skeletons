<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Processors\Processor;

use Shudd3r\Skeletons\Processors\Processor;
use Shudd3r\Skeletons\Environment\Files\File;
use Shudd3r\Skeletons\Replacements\Token;
use Shudd3r\Skeletons\Templates\Template;


class GenerateFile implements Processor
{
    private Template $template;
    private File     $file;

    public function __construct(Template $template, File $file)
    {
        $this->template = $template;
        $this->file     = $file;
    }

    public function process(Token $token): bool
    {
        $contents = $this->template->render($token);
        $this->file->write($contents);
        return true;
    }
}
