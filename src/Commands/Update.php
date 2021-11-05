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

use Shudd3r\Skeletons\Replacements\Reader;
use Shudd3r\Skeletons\Replacements\TokenCache;


class Update extends Factory
{
    public function command(array $options): Command
    {
        $files = $this->templates->generatedFiles(['init']);

        $isInteractive = isset($options['i']) || isset($options['interactive']);
        $metaFilename  = $this->env->metaDataFile()->name();

        $cache            = new TokenCache();
        $updateTokens     = new Reader\UpdateReader($this->replacements, $this->env, $options);
        $validationTokens = new Reader\ValidationReader($this->replacements, $this->env, $options);

        $metaDataExists    = new Precondition\CheckFileExists($this->env->metaDataFile());
        $validateFiles     = new Precondition\SkeletonSynchronization($validationTokens, $this->filesValidator($files, $cache));
        $validReplacements = new Precondition\ValidReplacements($updateTokens);
        $preconditions     = new Precondition\Preconditions(
            $this->checkInfo('Checking meta data status (`' . $metaFilename . '` should exist)', $metaDataExists),
            $this->checkInfo('Checking skeleton files synchronization:', $validateFiles, false),
            $this->checkInfo('Gathering replacement values', $validReplacements, !$isInteractive)
        );

        $saveMetaData  = new Command\SaveMetaData($updateTokens, $this->env->metaData());
        $generateFiles = new Command\ProcessTokens($updateTokens, $this->filesGenerator($files, $cache), $this->env->output());
        $command       = new Command\CommandSequence(
            $this->commandInfo('Updating skeleton files:', $generateFiles),
            $this->commandInfo('Updating meta data file (`' . $metaFilename . '`)', $saveMetaData)
        );

        return new Command\ProtectedCommand($command, $preconditions, $this->env->output());
    }
}
