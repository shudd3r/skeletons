<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons;

use Shudd3r\Skeletons\Setup\EnvSetup;
use Shudd3r\Skeletons\Setup\AppSetup;
use Shudd3r\Skeletons\Setup\ReplacementSetup;
use Shudd3r\Skeletons\Setup\TemplateSetup;
use Shudd3r\Skeletons\Environment\FileSystem\Directory;
use Shudd3r\Skeletons\Environment\Terminal;
use Exception;


class Application
{
    private const VERSION = '0.1';

    private EnvSetup $envSetup;
    private AppSetup $appSetup;
    private Terminal $terminal;

    public function __construct(Directory $package, Directory $skeleton, Terminal $terminal = null)
    {
        $this->envSetup = new EnvSetup($package, $skeleton);
        $this->appSetup = new AppSetup();
        $this->terminal = $terminal ?? new Terminal();
    }

    public function backup(Directory $backup): void
    {
        $this->envSetup->setBackupDirectory($backup);
    }

    public function metaFile(string $filename): void
    {
        $this->envSetup->setMetaFile($filename);
    }

    public function replacement(string $placeholder): ReplacementSetup
    {
        return new ReplacementSetup($this->appSetup, $placeholder);
    }

    public function template(string $filename): TemplateSetup
    {
        return new TemplateSetup($this->appSetup, $filename);
    }

    /**
     * @param string $command Command name (usually first CLI argument)
     * @param array  $options Command options
     *
     * @return int Exit code where 0 means execution without errors
     */
    public function run(string $command, array $options = []): int
    {
        $interactive = isset($options['i']) || isset($options['interactive']);
        $this->displayHeader($interactive && in_array($command, ['init', 'update']));

        try {
            $factory = $this->factory($command, $options);
            $command = $factory->command($options);

            $command->execute();
        } catch (Exception $e) {
            $this->terminal->send($e->getMessage() . PHP_EOL, 1);
        }

        $exitCode = $this->terminal->exitCode();
        $summary  = $exitCode ? 'Aborted (ERRORS)' : 'Done (OK)';
        $this->terminal->send(PHP_EOL . $summary . PHP_EOL);

        return $exitCode;
    }

    protected function factory(string $command, array $options): Commands
    {
        switch ($command) {
            case 'init':
                $env = $this->runtimeEnv();
                return new Commands\Initialize($env, $this->replacements(), $this->templates($env));
            case 'check':
                $env = $this->runtimeEnv(isset($options['remote']) ? ['local', 'init'] : ['init']);
                return new Commands\Validate($env, $this->replacements(), $this->templates($env));
            case 'update':
                $env = $this->runtimeEnv(['init', 'local']);
                return new Commands\Update($env, $this->replacements(), $this->templates($env));
        }

        throw new Exception("Unknown `{$command}` command");
    }

    private function replacements(): Replacements
    {
        return $this->appSetup->replacements();
    }

    private function runtimeEnv(array $ignoreTemplates = []): RuntimeEnv
    {
        return $this->envSetup->runtimeEnv($this->terminal, $ignoreTemplates);
    }

    private function templates(RuntimeEnv $env): Templates
    {
        return $this->appSetup->templates($env);
    }

    private function displayHeader(bool $isInteractive): void
    {
        $this->terminal->send(PHP_EOL);
        $this->terminal->send('------------------------------------------------------------' . PHP_EOL);
        $this->terminal->send('Shudd3r/Skeletons (' . self::VERSION . ')' . PHP_EOL);
        $this->terminal->send('Package skeleton template & validation system' . PHP_EOL);
        $isInteractive &&
        $this->terminal->send('Interactive input mode (press ctrl-c to abort)' . PHP_EOL);
        $this->terminal->send('------------------------------------------------------------' . PHP_EOL);
    }
}
