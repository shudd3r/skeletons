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
use Shudd3r\Skeletons\Replacements\TokenCache;


class Update extends Factory
{
    public function command(InputArgs $args): Command
    {
        $files            = $this->templates->generatedFiles($args);
        $dummies          = $this->templates->dummyFiles();
        $cache            = new TokenCache();
        $validationTokens = new Reader\ValidationReader($this->replacements, $this->env, $args);
        $validator        = $this->filesProcessor($files, $this->fileValidators($cache));
        $updateTokens     = new Reader\UpdateReader($this->replacements, $this->env, $args);
        $generator        = $this->filesProcessor($files, $this->fileGenerators($cache));

        $metaDataExists    = new Precondition\CheckFileExists($this->env->metaDataFile());
        $validMetaData     = new Precondition\ValidReplacements($validationTokens, $this->output);
        $validateFiles     = new Precondition\SkeletonSynchronization($validationTokens, $validator);
        $validReplacements = new Precondition\ValidReplacements($updateTokens, $this->output);
        $preconditions     = new Precondition\Preconditions(
            $this->checkInfo('Looking for meta data file (`' . $this->metaFile . '`)', $metaDataExists),
            $this->checkInfo('Validating meta data replacements', $validMetaData, ['OK']),
            $this->checkInfo('Checking skeleton files synchronization:', $validateFiles, []),
            $this->checkInfo('Gathering replacement values', $validReplacements, $args->interactive() ? [] : ['OK'])
        );

        $saveMetaData  = new Command\SaveMetaData($updateTokens, $this->env->metaData());
        $generateFiles = new Command\ProcessTokens($updateTokens, $generator, $this->output);
        $command       = new Command\CommandSequence(
            $this->commandInfo('Updating skeleton files:', $generateFiles),
            new Command\HandleDummyFiles($this->env->package(), $dummies, $this->output),
            $this->commandInfo('Updating meta data file (`' . $this->metaFile . '`)', $saveMetaData)
        );

        return new Command\ProtectedCommand($command, $preconditions, $this->output);
    }
}
