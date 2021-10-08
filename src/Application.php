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
use Shudd3r\PackageFiles\Application\Setup\EnvSetup;
use Shudd3r\PackageFiles\Application\Setup\ReplacementSetup;
use Shudd3r\PackageFiles\Application\Setup\TemplateSetup;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Environment\Terminal;
use Exception;


class Application
{
    private EnvSetup  $setup;
    private Terminal  $terminal;

    public function __construct(Directory $package, Directory $skeleton, Terminal $terminal = null)
    {
        $this->setup    = new EnvSetup($package, $skeleton);
        $this->terminal = $terminal ?? new Terminal();
    }

    public function backup(Directory $backup): void
    {
        $this->setup->setBackupDirectory($backup);
    }

    public function metaFile(string $filename): void
    {
        $this->setup->setMetaFile($filename);
    }

    public function replacement(string $placeholder): ReplacementSetup
    {
        return new ReplacementSetup($this->setup, $placeholder);
    }

    public function template(string $filename): TemplateSetup
    {
        return new TemplateSetup($this->setup, $filename);
    }

    /**
     * @param string $command Command name (usually first CLI argument)
     * @param array  $options Command options
     *
     * @return int Exit code where 0 means execution without errors
     */
    public function run(string $command, array $options = []): int
    {
        try {
            $env     = $this->setup->runtimeEnv($this->terminal);
            $factory = $this->factory($command, $env);
            $factory->command($options)->execute();
        } catch (Exception $e) {
            $this->terminal->send($e->getMessage(), 1);
        }

        return $this->terminal->exitCode();
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
