<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Rework\Replacements\Reader;

Use Shudd3r\Skeletons\Rework\Replacements\Reader;
use Closure;


class InputReader extends Reader
{
    public function commandArgument(string $argumentName): string
    {
        return $this->args->valueOf($argumentName);
    }

    public function inputString(string $prompt, Closure $isValid): ?string
    {
        if (!$this->args->interactive()) { return null; }

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

    private function sendAbortMessage(): void
    {
        $abortMessage = <<<ABORT
            Invalid value. Try `help` command for information on this value format.
            Aborting...
        
        ABORT;
        $this->env->output()->send($abortMessage);
    }
}
