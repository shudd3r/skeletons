<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Templates\Template;

use Shudd3r\Skeletons\Templates\Template;
use Shudd3r\Skeletons\Replacements\Token;


class MergedJsonTemplate implements Template
{
    private Template $template;
    private string   $jsonString;
    private bool     $isSynchronized;

    /**
     * $isSynchronized parameter should be set to true when updating
     * templates with dynamic keys (containing placeholders) because
     * valid schema is required.
     *
     * Otherwise, merging algorithm would not be able to distinguish
     * missing key in original file (with additional key that is not
     * present in the template schema) from updated key that should
     * replace the old one.
     *
     * When synchronization can be assumed algorithm can treat keys
     * positionally recognizing mismatched key at current position
     * as an old value of updated schema that should be replaced.
     *
     * @param Template $template
     * @param string   $jsonString
     * @param bool     $isSynchronized
     */
    public function __construct(Template $template, string $jsonString, bool $isSynchronized)
    {
        $this->template       = $template;
        $this->jsonString     = $jsonString;
        $this->isSynchronized = $isSynchronized;
    }

    public function render(Token $token): string
    {
        $rendered     = $this->template->render($token);
        $templateData = json_decode($rendered, true);
        $packageData  = json_decode($this->jsonString, true);

        if (!$templateData || !is_array($packageData)) { return $rendered; }

        return $this->jsonString($templateData, $packageData);
    }

    private function jsonString(array $template, array $package): string
    {
        $mergedData = $this->mergedDataStructure($template, $package) ?? [];
        if (!$mergedData && !$this->isList($template)) { return "{}\n"; }

        return json_encode($mergedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    }

    private function mergedDataStructure(array $template, array $package): ?array
    {
        return $this->isList($template)
            ? $this->mergedListItems($template, $package)
            : $this->mergedAssocStructure($template, $package);
    }

    private function isList(array $data): bool
    {
        return is_int(array_key_first($data));
    }

    private function mergedAssocStructure(array $template, array $package): ?array
    {
        $merged = [];
        foreach ($template as $key => $value) {
            $mergedValue = is_array($value)
                ? $this->mergedDataStructure($value, $package[$key] ?? [])
                : $value ?? $package[$key] ?? null;
            if ($mergedValue === null) { continue; }

            $merged[$key] = $mergedValue;
            $usedKey = $this->isSynchronized ? array_key_first($package) : $key;
            unset($package[$usedKey]);
        }

        foreach ($package as $key => $value) {
            $merged[$key] = $value;
        }

        return $merged ?: null;
    }

    private function mergedListItems(array $items, array $package): ?array
    {
        $template = $this->extractedFirstItemTemplate($items);
        if ($this->isSynchronized) {
            $package = array_slice($package, count($items));
        }

        foreach ($package as $value) {
            if (in_array($value, $items)) { continue; }
            $items[] = $template ? $this->mergedAssocStructure($template, $value) : $value;
        }

        return $items ?: null;
    }

    private function extractedFirstItemTemplate(array &$items): ?array
    {
        $assocItems = is_array($items[0]) && !$this->isList($items[0]);
        if (!$assocItems) { return null; }

        $template = array_fill_keys(array_keys($items[0]), null);
        $items[0] = array_filter($items[0], fn($value) => !is_null($value));

        if (!$items[0]) { array_shift($items); }

        return $template;
    }
}
