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
use Shudd3r\Skeletons\Replacements\TokenCache;


class Update extends Factory
{
    public function command(InputArgs $args): Command
    {
        $files            = $this->templates->generatedFiles($args);
        $dummies          = $this->templates->dummyFiles();
        $cache            = new TokenCache();
        $validationTokens = new Tokens($this->replacements, new Reader\DataReader($this->env, $args));
        $validator        = $this->filesProcessor($files, $this->fileValidators($cache));
        $updateTokens     = new Tokens($this->replacements, new Reader\InputReader($this->env, $args));
        $generator        = $this->filesProcessor($files, $this->fileGenerators($cache));

        $metaDataExists    = new Precondition\CheckFileExists($this->env->metaDataFile());
        $validMetaData     = new Precondition\ValidReplacements($validationTokens, $this->output);
        $validateFiles     = new Precondition\SkeletonSynchronization($validationTokens, $validator);
        $validReplacements = new Precondition\ValidReplacements($updateTokens, $this->output);
        $preconditions     = new Precondition\Preconditions(
            $this->checkInfo($metaDataExists, 'Looking for meta data file (`' . $this->metaFile . '`)', 8),
            $this->checkInfo($validMetaData, 'Validating meta data replacements', 16, ['OK']),
            $this->checkInfo($validateFiles, 'Checking skeleton files synchronization:', 1, []),
            $this->checkInfo($validReplacements, 'Gathering replacement values', 4, $args->interactive() ? [] : ['OK'])
        );

        $saveMetaData  = new Command\SaveMetaData($updateTokens, $this->env->metaData());
        $generateFiles = new Command\ProcessTokens($updateTokens, $generator, $this->output);
        $command       = new Command\CommandSequence(
            $this->commandInfo($generateFiles, 'Updating skeleton files:'),
            new Command\HandleDummyFiles($this->env->package(), $dummies, $this->output),
            $this->commandInfo($saveMetaData, 'Updating meta data file (`' . $this->metaFile . '`)')
        );

        return new Command\ProtectedCommand($command, $preconditions, $this->output);
    }
}
