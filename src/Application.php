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
use Shudd3r\Skeletons\Rework\Setup\AppSetup;
use Shudd3r\Skeletons\Rework\Setup\ReplacementSetup;
use Shudd3r\Skeletons\Setup\TemplateSetup;
use Shudd3r\Skeletons\Environment\Files\Directory;
use Shudd3r\Skeletons\Environment\Terminal;
use Exception;


class Application
{
    private const VERSION = '0.2.0';

    private EnvSetup $envSetup;
    private AppSetup $appSetup;
    private Terminal $terminal;
    private string   $skeletonName;

    public function __construct(Directory $package, Directory $skeleton, Terminal $terminal = null)
    {
        $this->envSetup = new EnvSetup($package, $skeleton);
        $this->appSetup = new AppSetup();
        $this->terminal = $terminal ?? new Terminal();
    }

    public function run(InputArgs $args): int
    {
        $this->terminal->send($this->headerMessage($args));

        try {
            $this->factory($args->command())->command($args)->execute();
        } catch (Exception $e) {
            $this->terminal->send($e->getMessage() . PHP_EOL, 128);
        }

        $exitCode = $this->terminal->exitCode();
        $this->terminal->send($this->summaryMessage($exitCode, $args->command()));

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

    public function skeletonName(string $name): void
    {
        if (!$name) { return; }
        $this->skeletonName = $name;
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

    private function headerMessage(InputArgs $args): string
    {
        $lines = [
            $this->skeletonName ?? ucfirst($args->script()),
            'Skeleton template & validation system',
            sprintf('powered by Shudd3r/Skeletons (%s)', self::VERSION)
        ];

        if ($args->interactive()) {
            $lines[] = 'Interactive input mode (press ctrl-c to abort)';
        }

        $length = 60;
        foreach ($lines as &$line) {
            $line = str_pad($line, $length, " ", STR_PAD_BOTH);
        }
        $separator = PHP_EOL . str_repeat('-', $length) . PHP_EOL;

        return $separator . implode(PHP_EOL, $lines) . $separator;
    }

    private function summaryMessage(int $exitCode, string $command): string
    {
        if ($command === 'help') { return ''; }
        $messages = [
            0   => $command === 'check' ? 'Package files match skeleton (OK)' : 'Done (OK)',
            1   => 'Package files do not match skeleton (FAIL)',
            2   => 'Process aborted',
            3   => 'Unable to update package not matching skeleton (ABORTED)',
            6   => 'Invalid input (ABORTED)',
            10  => $command === 'init' ? 'Package already initialized (ABORTED)' : 'Missing meta-data file (ABORTED)',
            18  => 'Invalid meta-data file (ABORTED)',
            34  => 'Unsafe backup operation (ABORTED)',
            128 => 'Application error: Exited unexpectedly (ERROR)'
        ];

        $message = $messages[$exitCode] ?? 'Process finished';
        if ($exitCode & 2) {
            $message = 'Precondition failure: ' . $message;
        }

        return PHP_EOL . $message . PHP_EOL;
    }
}
