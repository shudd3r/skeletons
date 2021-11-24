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

use Shudd3r\Skeletons\InputArgs;
use Shudd3r\Skeletons\Replacements;
use Shudd3r\Skeletons\RuntimeEnv;


abstract class Reader implements Reader\FallbackReader
{
    protected Replacements $replacements;
    protected RuntimeEnv   $env;

    /** @var ?Token\ValueToken[] */
    protected array $tokens = [];

    private InputArgs $args;

    public function __construct(RuntimeEnv $env, InputArgs $args)
    {
        $this->env  = $env;
        $this->args = $args;
    }

    public function tokens(Replacements $replacements): array
    {
        if ($this->tokens) { return $this->tokens; }
        $this->replacements = $replacements;

        foreach ($this->replacements->placeholders() as $placeholder) {
            $success = $this->readToken($placeholder);
            if (!$success && $this->args->interactive()) { break; }
        }

        return $this->tokens;
    }

    public function valueOf(string $name): string
    {
        if (!array_key_exists($name, $this->tokens)) {
            $this->readToken($name);
        }

        $token = $this->tokens[$name] ?? null;
        return $token ? $token->value() : '';
    }

    abstract protected function readToken(string $name): bool;

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

    protected function metaDataValue(string $namespace): string
    {
        return $this->env->metaData()->value($namespace) ?? '';
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
