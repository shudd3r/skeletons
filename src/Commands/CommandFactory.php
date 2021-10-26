<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Commands;

use Shudd3r\PackageFiles\Commands;
use Shudd3r\PackageFiles\RuntimeEnv;
use Shudd3r\PackageFiles\Setup\AppSetup;
use Shudd3r\PackageFiles\Templates;
use Shudd3r\PackageFiles\Replacements;
use Shudd3r\PackageFiles\Replacements\TokenCache;
use Shudd3r\PackageFiles\Processors\Processor;
use Shudd3r\PackageFiles\Processors\FileValidators;
use Shudd3r\PackageFiles\Processors\FileGenerators;
use Shudd3r\PackageFiles\Commands\Command\DescribedCommand;
use Shudd3r\PackageFiles\Commands\Precondition\DescribedPrecondition;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;


abstract class CommandFactory implements Commands
{
    protected RuntimeEnv $env;

    private AppSetup     $setup;
    private Templates    $templates;
    private Replacements $replacements;
    private Directory    $files;
    private Directory    $backup;

    public function __construct(RuntimeEnv $env, AppSetup $setup)
    {
        $this->env   = $env;
        $this->setup = $setup;
    }

    abstract public function command(array $options): Command;

    protected function replacements(): Replacements
    {
        return $this->replacements ??= $this->setup->replacements();
    }

    protected function files(): Directory
    {
        return $this->files ??= new Directory\ReflectedDirectory($this->env->package(), $this->env->skeleton());
    }

    protected function backup(): Directory
    {
        return $this->backup ??= new Directory\ReflectedDirectory($this->env->backup(), $this->files());
    }

    protected function filesValidator(TokenCache $tokenCache = null): Processor
    {
        return new Processor\FilesProcessor($this->files(), $this->templates(), new FileValidators($tokenCache));
    }

    protected function filesGenerator(TokenCache $tokenCache = null): Processor
    {
        return new Processor\FilesProcessor($this->files(), $this->templates(), new FileGenerators($tokenCache));
    }

    protected function commandInfo(string $message, Command $command): Command
    {
        return new DescribedCommand($command, $this->env->output(), $message);
    }

    protected function checkInfo(string $message, Precondition $precondition, bool $status = true): Precondition
    {
        return new DescribedPrecondition($precondition, $this->env->output(), $message, $status);
    }

    private function templates(): Templates
    {
        return $this->templates ??= $this->setup->templates($this->env);
    }
}
