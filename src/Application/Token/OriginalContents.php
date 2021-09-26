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


class OriginalContents implements Token
{
    public const PLACEHOLDER = 'original.content';

    protected string $contents;

    public function __construct(string $contents)
    {
        $this->contents = $contents;
    }

    public function replace(string $template): string
    {
        $token = $this->token($template);
        return $token->replace($template);
    }

    public function token(string $mask): Token
    {
        $placeholder = '{' . self::PLACEHOLDER . '}';

        $hasPlaceholder = strpos($mask, $placeholder) !== false;
        if (!$hasPlaceholder) { return $this->newTokenInstance(); }

        $fixedParts = explode($placeholder, $mask);
        if (!$this->contents) { return $this->newTokenInstance(); }

        $contents = $this->trimmedContents($this->contents, array_shift($fixedParts), array_pop($fixedParts));

        $clips = [];
        foreach ($fixedParts as $fixedPart) {
            [$originalClip, $contents] = explode($fixedPart, $contents, 2) + ['', ''];
            $clips[] = $originalClip;
        }
        $clips[] = $contents;

        return $this->newTokenInstance($clips);
    }

    protected function newTokenInstance(array $values = null): Token
    {
        if (!$values) {
            return new ValueToken(self::PLACEHOLDER, '');
        }

        return count($values) === 1
            ? new ValueToken(self::PLACEHOLDER, $values[0])
            : new ValueListToken(self::PLACEHOLDER, ...$values);
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
