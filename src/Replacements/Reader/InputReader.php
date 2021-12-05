<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Replacements\Reader;

Use Shudd3r\Skeletons\Replacements\Reader;


class InputReader extends Reader
{
    public function sendMessage(string $message): void
    {
        $this->env->output()->send('    ' . $this->formattedMessage($message) . PHP_EOL);
    }

    public function inputValue(string $prompt): ?string
    {
        if (!$this->args->interactive()) { return null; }
        return $this->env->input()->value('  > ' . $this->formattedMessage($prompt) . ':');
    }

    public function commandArgument(string $argumentName): ?string
    {
        return $this->args->valueOf($argumentName);
    }

    protected function readTokens(array $tokenNames): void
    {
        foreach ($tokenNames as $name) {
            if ($this->token($name) || !$this->args->interactive()) { continue; }
            $this->sendMessage('Aborting...');
            break;
        }
    }

    private function formattedMessage(string $message): string
    {
        return str_replace("\n", PHP_EOL . '    ', $message);
    }
}
