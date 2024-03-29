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
use Shudd3r\Skeletons\Replacements\Tokens;


class Validate extends Factory
{
    public function command(InputArgs $args): Command
    {
        $files     = $this->templates->generatedFiles($args);
        $tokens    = new Tokens($this->replacements, new Reader($this->env, $args, false));
        $processor = $this->filesProcessor($files, $this->fileValidators());

        $metaDataExists = new Precondition\CheckFileExists($this->env->metaDataFile());
        $validMetaData  = new Precondition\ValidReplacements($tokens, $this->output);
        $precondition   = new Precondition\Preconditions(
            $this->checkInfo($metaDataExists, 'Looking for meta data file (`' . $this->metaFile . '`)', 8),
            $this->checkInfo($validMetaData, 'Validating meta data replacements', 16, ['OK'])
        );

        $processTokens = new Command\ProcessTokens($tokens, $processor, $this->output);
        $command       = new Command\CommandSequence(
            $this->commandInfo($processTokens, 'Checking skeleton files synchronization:'),
            new Command\ValidateDummyFiles($this->env->package(), $this->dummyFiles(), $this->output)
        );

        return new Command\ProtectedCommand($command, $precondition, $this->output);
    }
}
