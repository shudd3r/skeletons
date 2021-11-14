<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Commands;

use Shudd3r\Skeletons\InputArgs;
use Shudd3r\Skeletons\Replacements\Reader;


class Validate extends Factory
{
    public function command(InputArgs $args): Command
    {
        $files     = $this->templates->generatedFiles($args);
        $dummies   = $this->templates->dummyFiles();
        $tokens    = new Reader\ValidationReader($this->replacements, $this->env, $args);
        $processor = $this->filesProcessor($files, $this->fileValidators());

        $metaDataExists = new Precondition\CheckFileExists($this->env->metaDataFile());
        $validMetaData  = new Precondition\ValidReplacements($tokens, $this->output);
        $precondition   = new Precondition\Preconditions(
            $this->checkInfo('Looking for meta data file (`' . $this->metaFile . '`)', $metaDataExists),
            $this->checkInfo('Validating meta data replacements', $validMetaData, ['OK'])
        );

        $processTokens = new Command\ProcessTokens($tokens, $processor, $this->output);
        $command       = new Command\CommandSequence(
            $this->commandInfo('Checking skeleton files synchronization:', $processTokens),
            new Command\HandleDummyFiles($this->env->package(), $dummies, $this->output, true)
        );

        return new Command\ProtectedCommand($command, $precondition, $this->output);
    }
}
