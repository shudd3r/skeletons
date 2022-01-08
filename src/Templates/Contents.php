<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Templates;

use Shudd3r\Skeletons\Environment\Files;


class Contents
{
    private string        $filename;
    private TemplateFiles $templates;
    private Files         $package;

    public function __construct(string $filename, TemplateFiles $templates, Files $package)
    {
        $this->filename  = $filename;
        $this->templates = $templates;
        $this->package   = $package;
    }

    public function template(): string
    {
        return $this->templates->file($this->filename)->contents();
    }

    public function package(): string
    {
        return $this->package->file($this->filename)->contents();
    }
}
