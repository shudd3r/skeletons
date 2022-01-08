<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons;

use Shudd3r\Skeletons\Environment\Files;
use Shudd3r\Skeletons\Templates\TemplateFiles;
use Shudd3r\Skeletons\Templates\Template;
use Shudd3r\Skeletons\Templates\Contents;
use Closure;


class Templates
{
    private Files         $package;
    private TemplateFiles $files;

    /** @var Closure[] fn (Contents) => Template */
    private array $factories;

    public function __construct(Files $package, TemplateFiles $templates, array $factories)
    {
        $this->package   = $package;
        $this->files     = $templates;
        $this->factories = $factories;
    }

    public function template(string $filename): Template
    {
        $create = $this->factories[$filename] ?? fn (Contents $c) => new Template\BasicTemplate($c->template());
        return $create(new Contents($filename, $this->files, $this->package));
    }

    public function generatedFiles(InputArgs $args): Files
    {
        $types = $args->command() === 'init' ? ['orig', 'init'] : ['orig'];
        if ($args->includeLocalFiles()) { $types[] = 'local'; }

        return new Files\ReflectedFiles($this->package, $this->files->files($types));
    }

    public function dummyFiles(): Files
    {
        return $this->files->files(['dummy']);
    }
}
