<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Replacements\Replacement;

use Shudd3r\Skeletons\Replacements\Source;
use Shudd3r\Skeletons\Replacements\Token;


class PackageName extends StandardReplacement
{
    protected ?string $inputPrompt  = 'Package name';
    protected ?string $argumentName = 'package';
    protected string  $description  = <<<'DESC'
        Package name [format: <vendor>/<package>]
        Replaces {%s} placeholder with its value directly, and
        {%s.composer} with its packagist normalized, lowercase
        version.
        DESC;

    protected function tokenInstance($name, $value): Token
    {
        return Token\CompositeToken::withValueToken(
            new Token\BasicToken($name, $value),
            new Token\BasicToken($name . '.composer', strtolower($value))
        );
    }

    protected function isValid(string $value): bool
    {
        return (bool) preg_match('#^[a-z0-9](?:[_.-]?[a-z0-9]+)*/[a-z0-9](?:[_.-]?[a-z0-9]+)*$#iD', $value);
    }

    protected function resolvedValue(Source $source): string
    {
        $packageName = $source->composer()->value('name') ?? $this->directoryFallback($source->packagePath());
        return $this->capitalized($packageName);
    }

    private function directoryFallback(string $path): string
    {
        return $path ? basename(dirname($path)) . '/' . basename($path) : '';
    }

    private function capitalized(string $value): string
    {
        $value = ucfirst($value);
        foreach (['/', '-', '.', '_'] as $separator) {
            $words = explode($separator, $value);
            if (count($words) === 1) { continue; }
            $value = implode($separator, array_map(fn (string $word) => ucfirst($word), $words));
        }

        return $value;
    }
}
