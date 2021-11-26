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


abstract class Reader implements Reader\FallbackReader
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

    public function valueOf(string $name): string
    {
        $token = $this->token($name);
        return $token ? $token->value() : '';
    }

    abstract protected function readToken(string $name, Replacement $replacement): ?Token;

    protected function commandLineOption(Replacement $replacement): ?string
    {
        $optionName = $replacement->optionName();
        return $optionName ? $this->args->valueOf($optionName) ?: null : null;
    }

    protected function inputString(Replacement $replacement, string $default): string
    {
        $prompt = $replacement->inputPrompt();
        if (!$prompt || !$this->args->interactive()) { return $default; }

        if (!$replacement->isValid($default)) { $default = null; }
        $prompt = $default ? $prompt . ' [default: `' . $default . '`]: ' : $prompt . ': ';

        $input = $this->fallbackInput($prompt, $default);
        $retry = 2;
        while (!$replacement->isValid($input) && $retry--) {
            $retryInfo = $retry === 0 ? 'once more' : 'again';
            $this->env->output()->send('    Invalid value. Try ' . $retryInfo . PHP_EOL);
            $input = $this->fallbackInput($prompt, $default);
        }

        if ($retry < 0) { $this->sendAbortMessage(); }
        return $input;
    }

    protected function defaultValue(Replacement $replacement): string
    {
        return $replacement->defaultValue($this->env, $this);
    }

    protected function metaDataValue(string $name): ?string
    {
        return $this->env->metaData()->value($name);
    }

    private function token(string $name): ?Token
    {
        if (array_key_exists($name, $this->tokens)) { return $this->tokens[$name]; }
        $this->tokens[$name] = null;
        return $this->tokens[$name] = $this->readToken($name, $this->replacements->replacement($name));
    }

    private function fallbackInput(string $prompt, ?string $default): string
    {
        $value = $this->env->input()->value('  > ' . $prompt);
        return !$value && $default !== null ? $default : $value;
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
