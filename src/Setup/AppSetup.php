<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Setup;

use Shudd3r\Skeletons\Replacements;
use Shudd3r\Skeletons\Replacements\Replacement;
use Shudd3r\Skeletons\Templates;
use Shudd3r\Skeletons\RuntimeEnv;
use Shudd3r\Skeletons\Exception;


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
        return new Templates($env, new Templates\TemplateFiles($env->skeleton()), $this->templates);
    }

    public function addReplacement(string $placeholder, Replacement $replacement): void
    {
        if ($placeholder === Replacements\Token\OriginalContents::PLACEHOLDER) {
            $message = "Overwritten built-in `$placeholder` placeholder replacement - use different name";
            throw new Exception\ReplacementOverwriteException($message);
        }

        if (isset($this->replacements[$placeholder])) {
            $message = "Duplicated definition for `$placeholder` placeholder replacement";
            throw new Exception\ReplacementOverwriteException($message);
        }
        $this->replacements[$placeholder] = $replacement;
    }

    public function addTemplate(string $filename, Templates\Factory $template): void
    {
        if (isset($this->templates[$filename])) {
            $message = "Duplicated definition of `$filename` template";
            throw new Exception\TemplateOverwriteException($message);
        }
        $this->templates[$filename] = $template;
    }
}
