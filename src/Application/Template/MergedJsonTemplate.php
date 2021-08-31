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


class MergedJsonTemplate implements Template
{
    private Template $template;
    private File     $jsonFile;

    public function __construct(Template $template, File $packageJsonFile)
    {
        $this->template = $template;
        $this->jsonFile = $packageJsonFile;
    }

    public function render(Token $token): string
    {
        $rendered = $this->template->render($token);
        $template = json_decode($rendered, true);
        $package  = json_decode($this->jsonFile->contents(), true);

        return $template && $package ? $this->mergedJson($template, $package) . "\n" : $rendered;
    }

    private function mergedJson(array $template, array $package): string
    {
        $data = $this->mergedDataStructure($template, $package);
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    }

    private function mergedDataStructure(array $template, array $package): array
    {
        $merged = [];
        foreach ($template as $key => $value) {
            if ($value === null) {
                if (isset($package[$key])) {
                    $merged[$key] = $package[$key];
                }
                continue;
            }
            if (is_array($value)) {
                $value = $this->mergedDataStructure($value, $package[$key] ?? []);
            }
            $merged[$key] = $value;
            unset($package[$key]);
        }

        foreach ($package as $key => $value) {
            $merged[$key] = $value;
        }

        return $merged;
    }
}
