<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Rework\Replacements;

use Shudd3r\Skeletons\RuntimeEnv;
use Shudd3r\Skeletons\InputArgs;
use Shudd3r\Skeletons\Rework\Replacements;
use Shudd3r\Skeletons\Replacements\Data\ComposerJsonData;
use Shudd3r\Skeletons\Replacements\Token;
use Closure;


class Reader implements Source
{
    private RuntimeEnv $env;
    private InputArgs  $args;

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
    public function tokens(Replacements $replacements): array
    {
        if ($this->tokens) { return $this->tokens; }
        $this->replacements = $replacements;

        foreach ($this->replacements->placeholders() as $name) {
            if (!$this->token($name) && $this->args->interactive()) { break; }
        }

        return $this->tokens;
    }

    public function commandArgument(string $argumentName): string
    {
        return $this->args->valueOf($argumentName);
    }

    public function inputString(string $prompt, Closure $isValid): string
    {
        if (!$this->args->interactive()) { return ''; }

        $input = $this->env->input()->value($prompt);
        $retry = 2;
        while (!$isValid($input) && $retry--) {
            $retryInfo = $retry === 0 ? 'once more' : 'again';
            $this->env->output()->send('    Invalid value. Try ' . $retryInfo . PHP_EOL);
            $input = $this->env->input()->value($prompt);
        }

        if ($retry < 0) { $this->sendAbortMessage(); }
        return $input;
    }

    public function metaValueOf(string $name): ?string
    {
        return $this->env->metaData()->value($name);
    }

    public function composer(): ComposerJsonData
    {
        return $this->env->composer();
    }

    public function fileContents(string $filename): string
    {
        return $this->env->package()->file($filename)->contents();
    }

    public function packagePath(): string
    {
        return $this->env->package()->path();
    }

    public function tokenValueOf(string $name): string
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

    private function sendAbortMessage(): void
    {
        $abortMessage = <<<ABORT
            Invalid value. Try `help` command for information on this value format.
            Aborting...
        
        ABORT;
        $this->env->output()->send($abortMessage);
    }
}
