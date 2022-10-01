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
        vendor/bin/%s <command> [<options>] [<argument>=<value> ...]

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
          -i, --interactive Allows providing placeholder values for 'init' or
                            'update' using interactive shell
                            If no <argument>=<value> is provided interactive
                            mode for these commands is turned on by default
          -l, --local       This option enables processing of skeleton's local
                            dev environment files that are not deployed with the
                            package (like IDE config, git hooks etc.).
                            This option shouldn't be used for package validation
                            in remote repository context.
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
        $message = sprintf(
            $this->helpTemplate,
            $args->script(),
            $this->env->metaDataFile()->name(),
            $this->env->backup()->path(),
            $this->replacementsInfo()
        );
        return new Commands\Command\DisplayMessage($message, $this->env->output());
    }

    private function replacementsInfo(): string
    {
        $message = implode(PHP_EOL, $this->replacements->info());
        return $message
            ? PHP_EOL . 'Available <arguments> for placeholder <values>:' . PHP_EOL . $message
            : PHP_EOL . 'No available <arguments> for placeholder <values>';
    }
}
