<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Template;

use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Application\Token;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;


class MergedJsonTemplate implements Template
{
    private File      $templateFile;
    private Directory $packageFiles;
    private bool      $synchronized;

    public function __construct(File $templateFile, Directory $package, bool $synchronized)
    {
        $this->templateFile = $templateFile;
        $this->packageFiles = $package;
        $this->synchronized = $synchronized;
    }

    public function render(Token $token): string
    {
        $template     = $this->templateFile->contents();
        $rendered     = $token->replace($template);
        $templateData = json_decode($rendered, true);
        $packageFile  = $this->packageFiles->file($this->templateFile->name());
        $packageData  = json_decode($packageFile->contents(), true);

        return $templateData && $packageData ? $this->mergedJson($templateData, $packageData) . "\n" : $rendered;
    }

    private function mergedJson(array $template, array $package): string
    {
        $data = $this->mergedDataStructure($template, $package);
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private function mergedDataStructure(array $template, array $package): array
    {
        $merged = [];
        foreach ($template as $key => $value) {
            if ($value === null) {
                if (isset($package[$key])) {
                    $merged[$key] = $package[$key];
                    unset($package[$key]);
                }
                continue;
            }
            if (is_array($value)) {
                if ($this->isList($value)) {
                    $value = $this->mergeListItems($value, $package[$key] ?? []);
                } else {
                    $value = $this->mergedDataStructure($value, $package[$key] ?? []);
                }
            }
            $merged[$key] = $value;
            $usedKey = $this->synchronized ? array_key_first($package) : $key;
            unset($package[$usedKey]);
        }

        foreach ($package as $key => $value) {
            $merged[$key] = $value;
        }

        return $merged;
    }

    private function mergeListItems(array $items, array $package): array
    {
        $template = $this->extractedFirstItemTemplate($items);
        foreach ($package as $value) {
            if (!in_array($value, $items)) {
                $items[] = $template ? $this->mergedDataStructure($template, $value) : $value;
            }
        }

        return $items;
    }

    private function isList(array $data): bool
    {
        return is_int(array_key_first($data));
    }

    private function extractedFirstItemTemplate(array &$items): ?array
    {
        $assocItems = is_array($items[0]) && !$this->isList($items[0]);
        if (!$assocItems) { return null; }

        $template = array_fill_keys(array_keys($items[0]), null);
        $items[0] = array_filter($items[0], fn($value) => !is_null($value));

        return $template;
    }
}
