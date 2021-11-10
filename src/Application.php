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
use Shudd3r\Skeletons\Environment\Files\Directory;
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

    public function run(InputArgs $args): int
    {
        $this->displayHeader($args->interactive());

        try {
            $this->factory($args->command())->command($args)->execute();
        } catch (Exception $e) {
            $this->terminal->send($e->getMessage() . PHP_EOL, 1);
        }

        $exitCode = $this->terminal->exitCode();
        $summary  = $exitCode ? 'Aborted (ERRORS)' : 'Done (OK)';
        $this->terminal->send(PHP_EOL . $summary . PHP_EOL);

        return $exitCode;
    }

    public function replacement(string $placeholder): ReplacementSetup
    {
        return new ReplacementSetup($this->appSetup, $placeholder);
    }

    public function template(string $filename): TemplateSetup
    {
        return new TemplateSetup($this->appSetup, $filename);
    }

    public function backup(Directory $backup): void
    {
        $this->envSetup->setBackupDirectory($backup);
    }

    public function metaFile(string $filename): void
    {
        $this->envSetup->setMetaFile($filename);
    }

    protected function factory(string $command): Commands
    {
        $env          = $this->envSetup->runtimeEnv($this->terminal);
        $replacements = $this->appSetup->replacements();
        $templates    = $this->appSetup->templates($env);

        switch ($command) {
            case 'init':   return new Commands\Initialize($env, $replacements, $templates);
            case 'check':  return new Commands\Validate($env, $replacements, $templates);
            case 'update': return new Commands\Update($env, $replacements, $templates);
            case 'sync':   return new Commands\Synchronize($env, $replacements, $templates);
            case 'help':   return new Commands\Help($env, $replacements);
        }

        throw new Exception("Unknown `{$command}` command");
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
