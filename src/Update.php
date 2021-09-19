<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles;

use Shudd3r\PackageFiles\Application\Command;
use Shudd3r\PackageFiles\Application\Token\TokenCache;
use Shudd3r\PackageFiles\Application\Processor;


class Update extends Command\Factory
{
    public function command(array $options): Command
    {
        $validation = new Validate($this->env);
        $cache      = new TokenCache();

        $tokenReader   = $this->env->replacements()->update($options);
        $processTokens = new Command\TokenProcessor($tokenReader, $this->processor($cache), $this->env->output());
        $writeMetaData = new Command\WriteMetaData($tokenReader, $this->env->metaDataFile());

        $metaDataExists    = new Command\Precondition\CheckFileExists($this->env->metaDataFile(), true);
        $synchronizedFiles = $validation->synchronizedSkeleton($cache);

        return new Command\ProtectedCommand(
            new Command\CommandSequence($processTokens, $writeMetaData),
            new Command\Precondition\Preconditions($metaDataExists, $synchronizedFiles)
        );
    }

    protected function processor(TokenCache $cache): Processor
    {
        $generatorFactory = new Processor\FileProcessors\UpdatedFileGenerators($this->env->package(), $this->env->templates(), $cache);
        return new Processor\SkeletonFilesProcessor($this->env->skeleton(), $generatorFactory);
    }
}
