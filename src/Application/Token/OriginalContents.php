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

use Shudd3r\PackageFiles\Application\Token;
use Shudd3r\PackageFiles\Environment\FileSystem\File;


class OriginalContents implements Token
{
    public const PLACEHOLDER = '{original.content}';

    private File             $packageFile;
    private ?FilesTokenCache $cache;

    public function __construct(File $packageFile, FilesTokenCache $cache = null)
    {
        $this->packageFile = $packageFile;
        $this->cache       = $cache;
    }

    public function replacePlaceholders(string $template): string
    {
        $token = $this->token($template);
        return $token->replacePlaceholders($template);
    }

    public function token(string $mask): Token
    {
        $hasPlaceholder = strpos($mask, self::PLACEHOLDER) !== false;
        if (!$hasPlaceholder) { return $this->newTokenInstance(); }

        $fixedParts = explode(self::PLACEHOLDER, $mask);
        $contents   = $this->packageFile->contents();
        if (!$contents) { return $this->newTokenInstance(); }

        $contents = $this->trimmedContents($contents, array_shift($fixedParts), array_pop($fixedParts));

        $clips = [];
        foreach ($fixedParts as $fixedPart) {
            [$originalClip, $contents] = explode($fixedPart, $contents, 2) + ['', ''];
            $clips[] = $originalClip;
        }
        $clips[] = $contents;

        return $this->newTokenInstance($clips);
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

    private function newTokenInstance(array $values = null): Token
    {
        if (!$values) {
            return $this->cached(new ValueToken(self::PLACEHOLDER, ''));
        }

        return count($values) === 1
            ? $this->cached(new ValueToken(self::PLACEHOLDER, $values[0]))
            : $this->cached(new ValueListToken(self::PLACEHOLDER, ...$values));
    }

    private function cached(Token $token): Token
    {
        if ($this->cache) {
            $this->cache->add($this->packageFile, $token);
        }
        return $token;
    }
}
