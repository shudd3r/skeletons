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
use Shudd3r\Skeletons\Replacements\ReplacementBuilder;
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

    /** @var ?Replacement[] */
    private array $replacements = [];

    /** @var ReplacementBuilder[] */
    private array $builders = [];

    /** @var Templates\Factory[]  */
    private array $templates = [];

    public function replacements(): Replacements
    {
        foreach ($this->builders as $placeholder => $builder) {
            $this->replacements[$placeholder] = $builder->build();
        }
        return new Replacements($this->replacements);
    }

    public function templates(RuntimeEnv $env): Templates
    {
        return new Templates($env, $this->templateFiles($env), $this->templates);
    }

    public function addReplacement(string $placeholder, Replacement $replacement): void
    {
        $this->validatePlaceholder($placeholder);
        $this->replacements[$placeholder] = $replacement;
    }

    public function addBuilder(string $placeholder, ReplacementBuilder $builder): void
    {
        $this->validatePlaceholder($placeholder);
        $this->replacements[$placeholder] = null;
        $this->builders[$placeholder]     = $builder;
    }

    public function addTemplate(string $filename, Templates\Factory $template): void
    {
        if (isset($this->templates[$filename])) {
            $message = "Duplicated definition of `$filename` template";
            throw new Exception\TemplateOverwriteException($message);
        }
        $this->templates[$filename] = $template;
    }

    private function validatePlaceholder(string $placeholder): void
    {
        if ($placeholder === Replacements\Token\OriginalContents::PLACEHOLDER) {
            $message = "Overwritten built-in `$placeholder` replacement placeholder - use different name";
            throw new Exception\ReplacementOverwriteException($message);
        }

        if (array_key_exists($placeholder, $this->replacements)) {
            $message = "Duplicated definition for `$placeholder` placeholder replacement";
            throw new Exception\ReplacementOverwriteException($message);
        }
    }

    private function templateFiles(RuntimeEnv $env): Templates\TemplateFiles
    {
        $typeIndex = [];
        foreach ($env->skeleton()->fileList() as $originalFile) {
            $sourceName = $originalFile->name();
            $fileType   = $this->fileType($sourceName);
            $targetName = $this->targetFilename($sourceName, $fileType);

            $type = $fileType === 'file' ? 'orig' : $fileType;
            $typeIndex[$type][$targetName] = $sourceName;
        }

        return new Templates\TemplateFiles($env->skeleton(), $typeIndex);
    }

    private function fileType(string $filename): string
    {
        $extFound = strrpos($filename, self::EXT_PREFIX);
        if (!$extFound) { return 'orig'; }

        $directive = substr($filename, $extFound);
        if (!in_array($directive, self::EXT_TYPES)) { return 'orig'; }

        return substr($directive, strlen(self::EXT_PREFIX));
    }

    private function targetFilename(string $filename, string $type): string
    {
        if ($type === 'orig') { return $filename; }
        $realPathFilename = str_replace(self::EXT_DIR . '/', '/', $filename);
        if (strpos($realPathFilename, '//')) { $realPathFilename = $filename; }
        return substr($realPathFilename, 0, -strlen(self::EXT_PREFIX . $type));
    }
}
