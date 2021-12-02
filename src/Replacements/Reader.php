<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Replacements;

use Shudd3r\Skeletons\RuntimeEnv;
use Shudd3r\Skeletons\InputArgs;
use Shudd3r\Skeletons\Replacements;
use Shudd3r\Skeletons\Replacements\Data\ComposerJsonData;
use Closure;


abstract class Reader implements Source
{
    protected RuntimeEnv $env;
    protected InputArgs  $args;

    private Replacements $replacements;

    private array $tokens = [];

    public function __construct(RuntimeEnv $env, InputArgs $args)
    {
        $this->env  = $env;
        $this->args = $args;
    }

    /**
     * @return array<string, ?Token>
     */
    final public function tokens(Replacements $replacements): array
    {
        if ($this->tokens) { return $this->tokens; }
        $this->replacements = $replacements;

        foreach ($this->replacements->placeholders() as $name) {
            if (!$this->token($name) && $this->args->interactive()) { break; }
        }

        return $this->tokens;
    }

    abstract public function commandArgument(string $argumentName): ?string;

    abstract public function inputString(string $prompt, Closure $isValid = null, int $tries = 1): ?string;

    final public function metaValueOf(string $name): ?string
    {
        return $this->env->metaData()->value($name);
    }

    final public function composer(): ComposerJsonData
    {
        return $this->env->composer();
    }

    final public function fileContents(string $filename): string
    {
        return $this->env->package()->file($filename)->contents();
    }

    final public function packagePath(): string
    {
        return $this->env->package()->path();
    }

    final public function tokenValueOf(string $name): string
    {
        $token = $this->token($name);
        return $token ? $token->value() : '';
    }

    private function token(string $name): ?Token
    {
        if (array_key_exists($name, $this->tokens)) { return $this->tokens[$name]; }
        $this->tokens[$name] = null;
        $replacement = $this->replacements->replacement($name);
        return $replacement ? $this->tokens[$name] = $replacement->token($name, $this) : null;
    }
}
