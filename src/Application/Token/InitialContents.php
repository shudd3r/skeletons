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


class InitialContents implements Token
{
    private const CONTENT_START = '{original.content>>>';
    private const CONTENT_END   = '<<<original.content}';

    private bool $initialize;

    public function __construct(bool $initialize = true)
    {
        $this->initialize = $initialize;
    }

    public function replacePlaceholders(string $template): string
    {
        if (!$this->hasPlaceholder($template)) { return $template; }

        $replace = $this->initialize ? '$1' : OriginalContents::PLACEHOLDER;
        return preg_replace($this->pattern(), $replace, $template);
    }

    private function hasPlaceholder($template): bool
    {
        $open = strpos($template, self::CONTENT_START);
        if ($open === false) { return false; }

        $close = strpos($template, self::CONTENT_END);
        if ($close === false || $open > $close) { return false; }

        return true;
    }

    private function pattern(): string
    {
        $capture = $this->initialize ? '(.+?)' : '.+?';
        $pattern = preg_quote(self::CONTENT_START) . $capture . preg_quote(self::CONTENT_END);

        return '#' . $pattern . '#s';
    }
}
