<?php

namespace Shudd3r\Skeletons\Commands;

use Shudd3r\Skeletons\Commands;
use Shudd3r\Skeletons\RuntimeEnv;
use Shudd3r\Skeletons\Replacements;
use Shudd3r\Skeletons\InputArgs;


class Help implements Commands
{
    protected string $helpTemplate = <<<HELP
        Usage from package root directory:
        vendor/bin/%s <command> [<options>]%s

        Available <command> values:
            init   Generates file structure from skeleton template and
                   creates meta-data file (%s)
                   Overwritten non-empty files that does not match skeleton
                   are saved in backup directory
                   (%s)
            check  Verifies project synchronization with used skeleton and
                   current meta-data file
            sync   Generates missing & mismatched skeleton files with backup
                   on overwrite (see: init command)
            update Regenerates skeleton files in valid packages with new
                   placeholder values and updates meta-data file
            help   Displays this help message

        Application <options>:
            -i, --interactive Allows providing `init` or `update` placeholder
                              values using interactive shell
            -r, --remote      May be used so that only deployable skeleton
                              files were processed
        %s
        HELP;

    private RuntimeEnv   $env;
    private Replacements $replacements;

    public function __construct(RuntimeEnv $env, Replacements $replacements)
    {
        $this->env          = $env;
        $this->replacements = $replacements;
    }

    public function command(InputArgs $args): Command
    {
        $replacementsInfo = $this->replacementsInfo();
        $message = sprintf(
            $this->helpTemplate,
            $args->script(),
            $replacementsInfo ? ' [<argument>=<value> ...]' : '',
            $this->env->metaDataFile()->name(),
            $this->env->backup()->path(),
            $replacementsInfo
        );
        return new Commands\Command\DisplayMessage($message, $this->env->output());
    }

    private function replacementsInfo(): string
    {
        $message = implode(PHP_EOL . '    ', $this->replacements->info());
        return $message
            ? PHP_EOL . 'Available <arguments> for placeholder <values>:' . PHP_EOL . '    ' . $message
            : '';
    }
}
