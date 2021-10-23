<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Setup;

use Shudd3r\PackageFiles\Application\Replacements;
use Shudd3r\PackageFiles\Application\Replacements\Replacement;
use Shudd3r\PackageFiles\Application\Template\Factory;
use Shudd3r\PackageFiles\Application\Template\Templates;
use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Application\Exception;


class AppSetup
{
    private array $replacements = [];
    private array $templates    = [];

    public function replacements(): Replacements
    {
        return new Replacements($this->replacements);
    }

    public function templates(RuntimeEnv $env): Templates
    {
        return new Templates($env, $this->templates);
    }

    public function addReplacement(string $placeholder, Replacement $replacement): void
    {
        if (isset($this->replacements[$placeholder])) {
            throw new Exception\ReplacementOverwriteException();
        }
        $this->replacements[$placeholder] = $replacement;
    }

    public function addTemplate(string $filename, Factory $template): void
    {
        if (isset($this->templates[$filename])) {
            throw new Exception\TemplateOverwriteException();
        }
        $this->templates[$filename] = $template;
    }
}
