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


final class Reader implements Source
{
    private RuntimeEnv $env;
    private InputArgs  $args;
    private bool       $input;

    private Replacements $replacements;

    private array $tokens = [];

    public function __construct(RuntimeEnv $env, InputArgs $args, bool $userInput = true)
    {
        $this->env   = $env;
        $this->args  = $args;
        $this->input = $userInput;
    }

    /**
     * @return array<string, ?Token>
     */
    public function tokens(Replacements $replacements): array
    {
        if ($this->tokens) { return $this->tokens; }

        $this->replacements = $replacements;
        $this->readTokens($this->replacements->placeholders());

        return $this->tokens;
    }

    public function sendMessage(string $message): void
    {
        if (!$this->input) { return; }
        $this->env->output()->send('    ' . $this->formattedMessage($message) . PHP_EOL);
    }

    public function inputValue(string $prompt): ?string
    {
        if (!$this->input || !$this->args->interactive()) { return null; }
        return $this->env->input()->value('  > ' . $this->formattedMessage($prompt) . ':');
    }

    public function commandArgument(string $argumentName): ?string
    {
        return $this->input ? $this->args->valueOf($argumentName) : null;
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

    private function readTokens(array $tokenNames): void
    {
        $abortOnError = $this->input && $this->args->interactive();
        foreach ($tokenNames as $name) {
            if ($this->token($name) || !$abortOnError) { continue; }
            $this->sendMessage('Aborting...');
            break;
        }
    }

    private function token(string $name): ?Token
    {
        if (array_key_exists($name, $this->tokens)) { return $this->tokens[$name]; }
        $this->tokens[$name] = null;
        $replacement = $this->replacements->replacement($name);
        return $replacement ? $this->tokens[$name] = $replacement->token($name, $this) : null;
    }

    private function formattedMessage(string $message): string
    {
        return str_replace("\n", PHP_EOL . '    ', $message);
    }
}
