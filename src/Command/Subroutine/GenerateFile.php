<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Command\Subroutine;

use Shudd3r\PackageFiles\Command\Subroutine;
use Shudd3r\PackageFiles\Application\FileSystem\File;
use Shudd3r\PackageFiles\Properties;
use Shudd3r\PackageFiles\Template;


class GenerateFile implements Subroutine
{
    private Template $template;
    private File     $file;

    public function __construct(Template $template, File $file)
    {
        $this->template = $template;
        $this->file     = $file;
    }

    public function process(Properties $properties): void
    {
        $contents = $this->template->render($properties);
        $this->file->write($contents);
    }
}
