<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Processor;

use Shudd3r\PackageFiles\Application\Processor;
use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Environment\Output;
use Shudd3r\PackageFiles\Application\Token;


class CompareFile implements Processor
{
    private Template $template;
    private File     $file;
    private Output   $output;

    public function __construct(Template $template, File $file, Output $output)
    {
        $this->template = $template;
        $this->file     = $file;
        $this->output   = $output;
    }

    public function process(Token $token): void
    {
        $success = $this->template->render($token) === $this->file->contents();
        $success ? $this->successMessage() : $this->errorMessage();
    }

    private function successMessage(): void
    {
        $message = 'Checking file `%s` - OK';
        $this->output->send(sprintf($message, $this->file->name()));
    }

    private function errorMessage(): void
    {
        $message = 'Checking file `%s` - FAILED';
        $this->output->send(sprintf($message, $this->file->name()), 1);
    }
}
