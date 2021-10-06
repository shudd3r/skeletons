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
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Environment\Terminal;
use Exception;


class Application
{
    private Directory $package;
    private Directory $skeleton;
    private Directory $backup;
    private File      $metaData;
    private Terminal  $terminal;
    private array     $templates    = [];
    private array     $replacements = [];

    public function __construct(Directory $package, Directory $skeleton)
    {
        $this->package  = $package;
        $this->skeleton = $skeleton;
    }

    /**
     * @param string $command Command name (usually first CLI argument)
     * @param array  $options Command options
     *
     * @return int Exit code where 0 means execution without errors
     */
    public function run(string $command, array $options = []): int
    {
        $this->terminal ??= new Terminal();

        try {
            $env     = $this->runtimeEnv();
            $factory = $this->factory($command, $env);
            $factory->command($options)->execute();
        } catch (Exception $e) {
            $this->terminal->send($e->getMessage(), 1);
        }

        return $this->terminal->exitCode();
    }

    public function template(string $filename, callable $template): void
    {
        $this->templates[$filename] = $template;
    }

    public function replacement(string $placeholder, callable $replacement): void
    {
        $this->replacements[$placeholder] = $replacement;
    }

    public function terminal(Terminal $terminal): void
    {
        $this->terminal = $terminal;
    }

    public function backupDirectory(Directory $backup): void
    {
        $this->backup = $backup;
    }

    public function metaFile(string $filename): void
    {
        $this->metaData = $this->package->file($filename);
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

    private function runtimeEnv(): RuntimeEnv
    {
        $env = new RuntimeEnv(
            $this->package,
            $this->skeleton,
            $this->terminal,
            $this->backup   ??= $this->package->subdirectory('.skeleton-backup'),
            $this->metaData ??= $this->package->file('.github/skeleton.json')
        );

        $replacements = $env->replacements();
        foreach ($this->replacements as $placeholder => $replacement) {
            $replacements->add($placeholder, $replacement($env));
        }

        $templates = $env->templates();
        foreach ($this->templates as $filename => $template) {
            $templates->add($filename, $template($env));
        }

        return $env;
    }
}
