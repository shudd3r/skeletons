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


abstract class Reader
{
    protected Replacements $replacements;
    protected RuntimeEnv   $env;

    /** @var ?Token\ValueToken[] */
    protected array $tokens = [];

    private InputArgs $args;

    public function __construct(Replacements $replacements, RuntimeEnv $env, InputArgs $args)
    {
        $this->replacements = $replacements;
        $this->env          = $env;
        $this->args         = $args;
    }

    public function token(): ?Token
    {
        $tokens = $this->tokens();
        return !in_array(null, $tokens) ? new Token\CompositeToken(...array_values($tokens)) : null;
    }

    public function tokenValues(): array
    {
        $values = [];
        foreach ($this->tokens() as $name => $token) {
            $values[$name] = $token ? $token->value() : null;
        }

        return $values;
    }

    abstract public function readToken(string $name): bool;

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

    private function tokens(): array
    {
        if (!$this->tokens) { $this->replacements->tokens($this, $this->args->interactive()); }
        return $this->tokens;
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
