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

use Shudd3r\Skeletons\Replacements;


class Validate extends Factory
{
    public function command(array $options): Command
    {
        $files = $this->templates->generatedFiles(isset($options['remote']) ? ['local', 'init'] : ['init']);

        $metaFilename     = $this->env->metaDataFile()->name();
        $validationTokens = new Replacements\Reader\ValidationReader($this->replacements, $this->env, $options);

        $metaDataExists    = new Precondition\CheckFileExists($this->env->metaDataFile());
        $validReplacements = new Precondition\ValidReplacements($validationTokens);
        $precondition      = new Precondition\Preconditions(
            $this->checkInfo('Checking meta data status (`' . $metaFilename . '` should exist)', $metaDataExists),
            $this->checkInfo('Validating meta data replacements', $validReplacements)
        );

        $processTokens = new Command\ProcessTokens($validationTokens, $this->filesValidator($files), $this->env->output());
        $command       = $this->commandInfo('Checking skeleton files synchronization:', $processTokens);

        return new Command\ProtectedCommand($command, $precondition, $this->env->output());
    }
}
