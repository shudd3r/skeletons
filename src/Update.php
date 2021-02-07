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
use Shudd3r\PackageFiles\Environment\Command as CommandInterface;
use Shudd3r\PackageFiles\Application\Token\Reader;
use Shudd3r\PackageFiles\Application\Token\TokenCache;
use Shudd3r\PackageFiles\Application\Processor;
use Shudd3r\PackageFiles\Application\Template;


class Update extends Command\Factory
{
    public function command(): CommandInterface
    {
        $validation = new Validate($this->env, $this->options);
        $cache      = new TokenCache();

        $tokenReader   = new Reader\UpdateReader($this->tokenReaders());
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
        $composerFile     = $this->env->package()->file('composer.json');
        $template         = new Template\ComposerJsonTemplate($composerFile);
        $generateComposer = new Processor\GenerateFile($template, $composerFile);

        $generatorFactory = new Processor\FileProcessors\UpdatedFileGenerators($this->env->package(), $cache);
        $generatePackage  = new Processor\SkeletonFilesProcessor($this->env->skeleton(), $generatorFactory);

        return new Processor\ProcessorSequence($generateComposer, $generatePackage);
    }
}
