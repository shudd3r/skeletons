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
        $isInteractive = isset($options['i']) || isset($options['interactive']);

        $files            = $this->templates->generatedFiles(['init']);
        $cache            = new TokenCache();
        $validationTokens = new Reader\ValidationReader($this->replacements, $this->env, $options);
        $validator        = $this->filesProcessor($files, $this->fileValidators($cache));
        $updateTokens     = new Reader\UpdateReader($this->replacements, $this->env, $options);
        $generator        = $this->filesProcessor($files, $this->fileGenerators($cache));

        $metaDataExists    = new Precondition\CheckFileExists($this->env->metaDataFile());
        $validateFiles     = new Precondition\SkeletonSynchronization($validationTokens, $validator);
        $validReplacements = new Precondition\ValidReplacements($updateTokens);
        $preconditions     = new Precondition\Preconditions(
            $this->checkInfo('Checking meta data status (`' . $this->metaFile . '` should exist)', $metaDataExists),
            $this->checkInfo('Checking skeleton files synchronization:', $validateFiles, false),
            $this->checkInfo('Gathering replacement values', $validReplacements, !$isInteractive)
        );

        $saveMetaData  = new Command\SaveMetaData($updateTokens, $this->env->metaData());
        $generateFiles = new Command\ProcessTokens($updateTokens, $generator, $this->env->output());
        $command       = new Command\CommandSequence(
            $this->commandInfo('Updating skeleton files:', $generateFiles),
            $this->commandInfo('Updating meta data file (`' . $this->metaFile . '`)', $saveMetaData)
        );

        return new Command\ProtectedCommand($command, $preconditions, $this->env->output());
    }
}
