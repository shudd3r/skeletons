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
    private const EXT_PREFIX = '.sk_';
    private const EXT_DIR    = self::EXT_PREFIX . 'dir';
    private const EXT_TYPES  = [
        self::EXT_PREFIX . 'file',
        self::EXT_PREFIX . 'local',
        self::EXT_PREFIX . 'init'
    ];

    private array $replacements = [];
    private array $templates    = [];

    public function replacements(): Replacements
    {
        return new Replacements($this->replacements);
    }

    public function templates(RuntimeEnv $env): Templates
    {
        return new Templates($env, $this->templateFiles($env), $this->templates);
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

    private function templateFiles(RuntimeEnv $env): Templates\TemplateFiles
    {
        $typeIndex = [];
        foreach ($env->skeleton()->fileList() as $originalFile) {
            $originalName = $originalFile->name();
            [$type, $filename] = $this->targetFilename($originalName);
            $typeIndex[$type][$filename] = $originalName;
        }

        return new Templates\TemplateFiles($env->skeleton(), $typeIndex);
    }

    private function targetFilename(string $filename): array
    {
        $extFound = strrpos($filename, self::EXT_PREFIX);
        if (!$extFound) { return ['orig', $filename]; }

        $directive = substr($filename, $extFound);
        if (!in_array($directive, self::EXT_TYPES)) { return ['orig', $filename]; }
        $filename = $this->unlockedPath(substr($filename, 0, -strlen($directive)));

        $type = substr($directive, strlen(self::EXT_PREFIX));
        return [$type, $filename];
    }

    private function unlockedPath(string $filename): string
    {
        return str_replace(self::EXT_DIR, '', $filename);
    }
}
