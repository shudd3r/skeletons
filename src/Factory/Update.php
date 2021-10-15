<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Factory;

use Shudd3r\PackageFiles\Factory;
use Shudd3r\PackageFiles\Application\Command;
use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Application\Token\Reader;
use Shudd3r\PackageFiles\Application\Token\Replacements;
use Shudd3r\PackageFiles\Application\Token\TokenCache;
use Shudd3r\PackageFiles\Application\Processor;


class Update implements Factory
{
    private RuntimeEnv   $env;
    private Replacements $replacements;

    public function __construct(RuntimeEnv $env, Replacements $replacements)
    {
        $this->env          = $env;
        $this->replacements = $replacements;
    }

    public function command(array $options): Command
    {
        $cache = new TokenCache();

        $metaDataExists = new Command\Precondition\CheckFileExists($this->env->metaDataFile(), true);
        $preconditions  = new Command\Precondition\Preconditions($metaDataExists, $this->synchronizedSkeleton($cache));

        $reader        = new Reader\UpdateReader($this->replacements, $this->env, $options);
        $processTokens = new Command\TokenProcessor($reader, $this->processor($cache), $this->env->output());
        $saveMetaData  = new Command\SaveMetaData($reader, $this->env->metaData());
        $command       = new Command\CommandSequence($processTokens, $saveMetaData);

        return new Command\ProtectedCommand($command, $preconditions, $this->env->output());
    }

    protected function processor(TokenCache $cache): Processor
    {
        $generatorFactory = new Processor\FileProcessors\UpdatedFileGenerators($this->env->package(), $this->env->templates(), $cache);
        return new Processor\SkeletonFilesProcessor($this->env->skeleton(), $generatorFactory);
    }

    public function synchronizedSkeleton(TokenCache $cache): Command\Precondition
    {
        $reader         = new Reader\ValidationReader($this->replacements, $this->env, []);
        $fileValidators = new Processor\FileProcessors\CachingFileValidators($this->env->package(), $this->env->templates(), $cache);
        $tokenProcessor = new Processor\SkeletonFilesProcessor($this->env->skeleton(), $fileValidators);

        return new Command\Precondition\SkeletonSynchronization($reader, $tokenProcessor);
    }
}
