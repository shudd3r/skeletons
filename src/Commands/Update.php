<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Commands;

use Shudd3r\PackageFiles\Replacements\Reader;
use Shudd3r\PackageFiles\Replacements\TokenCache;


class Update extends CommandFactory
{
    public function command(array $options): Command
    {
        $cache            = new TokenCache();
        $updateTokens     = new Reader\UpdateReader($this->replacements(), $this->env, $options);
        $validationTokens = new Reader\ValidationReader($this->replacements(), $this->env, $options);

        $metaDataExists    = new Precondition\CheckFileExists($this->env->metaDataFile(), true);
        $validateFiles     = new Precondition\SkeletonSynchronization($validationTokens, $this->filesValidator($cache));
        $validReplacements = new Precondition\ValidReplacements($updateTokens);
        $preconditions     = new Precondition\Preconditions(
            $this->checkInfo('Checking meta data status', $metaDataExists),
            $this->checkInfo('Checking skeleton synchronization', $validateFiles),
            $this->checkInfo('Gathering replacement values', $validReplacements, false)
        );

        $saveMetaData  = new Command\SaveMetaData($updateTokens, $this->env->metaData());
        $generateFiles = new Command\TokenProcessor($updateTokens, $this->filesGenerator($cache), $this->env->output());
        $command       = new Command\CommandSequence(
            $this->commandInfo('Generating skeleton files', $generateFiles),
            $this->commandInfo('Generating meta data file', $saveMetaData)
        );

        return new Command\ProtectedCommand($command, $preconditions, $this->env->output());
    }
}
