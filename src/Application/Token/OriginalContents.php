<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token;

use Shudd3r\PackageFiles\Environment\FileSystem\File;


class OriginalContents
{
    public const PLACEHOLDER = '{original.content}';

    private File $packageFile;

    public function __construct(File $packageFile)
    {
        $this->packageFile = $packageFile;
    }

    public function clips(string $mask): array
    {
        $hasPlaceholder = strpos($mask, self::PLACEHOLDER) !== false;
        if (!$hasPlaceholder) { return []; }

        $fixedParts = explode(self::PLACEHOLDER, $mask);
        $contents   = $this->packageFile->contents();
        if (!$contents) {
            return array_fill(0, count($fixedParts) - 1, '');
        }

        $contents = $this->trimmedContents($contents, array_shift($fixedParts), array_pop($fixedParts));

        $clips = [];
        foreach ($fixedParts as $fixedPart) {
            [$originalClip, $contents] = explode($fixedPart, $contents, 2) + ['', ''];
            $clips[] = $originalClip;
        }
        $clips[] = $contents;

        return $clips;
    }

    private function trimmedContents(string $contents, string $prefix, string $postfix): string
    {
        if ($prefix) {
            $contents = substr($contents, strlen($prefix));
        }
        if ($postfix) {
            $contents = substr($contents, 0, -strlen($postfix));
        }
        return $contents;
    }
}
