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
        $tokens    = new Reader\ValidationReader($this->replacements, $this->env, $args);
        $processor = $this->filesProcessor($files, $this->fileValidators());

        $metaDataExists    = new Precondition\CheckFileExists($this->env->metaDataFile());
        $validReplacements = new Precondition\ValidReplacements($tokens);
        $precondition      = new Precondition\Preconditions(
            $this->checkInfo('Checking meta data status (`' . $this->metaFile . '` should exist)', $metaDataExists),
            $this->checkInfo('Validating meta data replacements', $validReplacements)
        );

        $processTokens = new Command\ProcessTokens($tokens, $processor, $this->env->output());
        $command       = $this->commandInfo('Checking skeleton files synchronization:', $processTokens);

        return new Command\ProtectedCommand($command, $precondition, $this->env->output());
    }
}
