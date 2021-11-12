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
use Shudd3r\Skeletons\Templates\Template;
use Shudd3r\Skeletons\Environment\Files;
use Shudd3r\Skeletons\Environment\Files\File;
use Shudd3r\Skeletons\Replacements\Token;


class CompareFile implements Processor
{
    private Template $template;
    private File     $file;
    private ?Files   $backup;

    public function __construct(Template $template, File $file, ?Files $backup = null)
    {
        $this->template = $template;
        $this->file     = $file;
        $this->backup   = $backup;
    }

    public function process(Token $token): bool
    {
        $fileContents = $this->file->contents();
        $synchronized = $this->template->render($token) === $fileContents;
        if ($this->backup && !$synchronized && $fileContents) {
            $this->backup->file($this->file->name())->write($fileContents);
        }
        return $synchronized && $this->file->exists();
    }
}
