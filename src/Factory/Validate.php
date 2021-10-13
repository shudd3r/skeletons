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
use Shudd3r\PackageFiles\Application\Token\Replacements;
use Shudd3r\PackageFiles\Application\Token\Reader;
use Shudd3r\PackageFiles\Application\Token\TokenCache;
use Shudd3r\PackageFiles\Application\Processor;


class Validate implements Factory
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
        $output         = $this->env->output();
        $metaDataExists = new Command\Precondition\CheckFileExists($this->env->metaDataFile(), true);
        $fileValidators = new Processor\FileProcessors\FileValidators($this->env->package(), $this->env->templates());
        $tokenProcessor = $this->processor($fileValidators);
        $processTokens  = new Command\TokenProcessor($this->tokenReader(), $tokenProcessor, $output);

        return new Command\ProtectedCommand($processTokens, $metaDataExists, $output);
    }

    public function synchronizedSkeleton(TokenCache $cache): Command\Precondition
    {
        $fileValidators = new Processor\FileProcessors\CachingFileValidators($this->env->package(), $this->env->templates(), $cache);
        $tokenProcessor = $this->processor($fileValidators);

        return new Command\Precondition\SkeletonSynchronization($this->tokenReader(), $tokenProcessor);
    }

    protected function processor(Processor\FileProcessors $fileValidators): Processor
    {
        return new Processor\SkeletonFilesProcessor($this->env->skeleton(), $fileValidators);
    }

    private function tokenReader(): Reader
    {
        return new Reader\ValidationReader($this->replacements, $this->env, []);
    }
}
