<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles;

use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Application\Command;
use Shudd3r\PackageFiles\Application\Template;
use Exception;


class Application
{
    private RuntimeEnv $env;
    private array      $templates    = [];
    private array      $replacements = [];

    public function __construct(RuntimeEnv $env)
    {
        $this->env = $env;
    }

    /**
     * @param string $command Command name (usually first CLI argument)
     * @param array  $options Command options
     *
     * @return int Exit code where 0 means execution without errors
     */
    public function run(string $command, array $options = []): int
    {
        $output = $this->env->output();

        $replacements = $this->env->replacements();
        foreach ($this->replacements as $placeholder => $replacement) {
            $replacements->add($placeholder, $replacement);
        }

        $templates = $this->env->templates();
        foreach ($this->templates as $filename => $template) {
            $templates->add($filename, $template);
        }

        try {
            $this->command($command, $options)->execute();
        } catch (Exception $e) {
            $output->send($e->getMessage(), 1);
        }

        return $output->exitCode();
    }

    public function template(string $filename, Template\Factory $template): void
    {
        $this->templates[$filename] = $template;
    }

    public function replacement(string $placeholder, Replacement $replacement): void
    {
        $this->replacements[$placeholder] = $replacement;
    }

    private function command(string $command, array $options): Command
    {
        return $this->factory($command, $this->env)->command($options);
    }

    protected function factory(string $command, RuntimeEnv $env): Factory
    {
        switch ($command) {
            case 'init':   return new Factory\Initialize($env);
            case 'check':  return new Factory\Validate($env);
            case 'update': return new Factory\Update($env);
        }

        throw new Exception("Unknown `{$command}` command");
    }
}
