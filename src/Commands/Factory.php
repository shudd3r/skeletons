<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Commands;

use Shudd3r\Skeletons\Commands;
use Shudd3r\Skeletons\RuntimeEnv;
use Shudd3r\Skeletons\Templates;
use Shudd3r\Skeletons\Replacements;
use Shudd3r\Skeletons\Replacements\TokenCache;
use Shudd3r\Skeletons\Processors;
use Shudd3r\Skeletons\Processors\Processor;
use Shudd3r\Skeletons\Commands\Command\DescribedCommand;
use Shudd3r\Skeletons\Commands\Precondition\DescribedPrecondition;
use Shudd3r\Skeletons\Environment\Files;


abstract class Factory implements Commands
{
    protected RuntimeEnv   $env;
    protected Replacements $replacements;
    protected Templates    $templates;

    public function __construct(RuntimeEnv $env, Replacements $replacements, Templates $templates)
    {
        $this->env          = $env;
        $this->replacements = $replacements;
        $this->templates    = $templates;
    }

    abstract public function command(array $options): Command;

    protected function filesValidator(Files $files, ?TokenCache $tokenCache = null, Files $backup = null): Processor
    {
        return $this->fileProcessor($files, new Processors\FileValidators($this->env->output(), $tokenCache, $backup));
    }

    protected function filesGenerator(Files $files, TokenCache $tokenCache = null): Processor
    {
        return $this->fileProcessor($files, new Processors\FileGenerators($this->env->output(), $tokenCache));
    }

    protected function commandInfo(string $message, Command $command): Command
    {
        return new DescribedCommand($command, $this->env->output(), $message);
    }

    protected function checkInfo(string $message, Precondition $precondition, bool $status = true): Precondition
    {
        return new DescribedPrecondition($precondition, $this->env->output(), $message, $status);
    }

    private function fileProcessor(Files $files, Processors $processors): Processor
    {
        return new Processor\FilesProcessor($files, $this->templates, $processors);
    }
}
