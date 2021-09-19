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
use Shudd3r\PackageFiles\Application\Command\Factory;
use Exception;


class Application
{
    private RuntimeEnv $env;

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

        try {
            $this->command($command, $options)->execute();
        } catch (Exception $e) {
            $output->send($e->getMessage(), 1);
        }

        return $output->exitCode();
    }

    private function command(string $command, array $options): Command
    {
        return $this->factory($command, $this->env)->command($options);
    }

    protected function factory(string $command, RuntimeEnv $env): Factory
    {
        switch ($command) {
            case 'init':   return new Initialize($env);
            case 'check':  return new Validate($env);
            case 'update': return new Update($env);
        }

        throw new Exception("Unknown `{$command}` command");
    }
}
