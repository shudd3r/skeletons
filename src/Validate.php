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
use Shudd3r\PackageFiles\Application\Processor;
use Shudd3r\PackageFiles\Application\Token\Reader;
use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Application\Token\TokenCache;
use Shudd3r\PackageFiles\Application\Template;


class Validate extends Command\Factory
{
    public function command(): CommandInterface
    {
        $metaDataExists = new Command\Precondition\CheckFileExists($this->env->metaDataFile(), true);
        $tokenReader    = new Reader\CompositeTokenReader($this->tokenReaders());
        $fileValidators = new Processor\FileProcessors\FileValidators($this->env->package());
        $tokenProcessor = $this->processor($fileValidators);
        $processTokens  = new Command\TokenProcessor($tokenReader, $tokenProcessor, $this->env->output());

        return new Command\ProtectedCommand($processTokens, $metaDataExists);
    }

    public function synchronizedSkeleton(TokenCache $cache): Command\Precondition
    {
        $tokenReader    = new Reader\CompositeTokenReader($this->tokenReaders());
        $fileValidators = new Processor\FileProcessors\CachingFileValidators($this->env->package(), $cache);
        $tokenProcessor = $this->processor($fileValidators);

        return new Command\Precondition\SkeletonSynchronization($tokenReader, $tokenProcessor);
    }

    protected function tokenReaders(): array
    {
        $source = new Source\MetaDataFile($this->env->metaDataFile(), new Source\PredefinedValue(''));

        return [
            $package = new Reader\PackageName($source),
            new Reader\RepositoryName($source),
            new Reader\PackageDescription($source),
            new Reader\SrcNamespace($source)
        ];
    }

    protected function processor(Processor\FileProcessors $fileValidators): Processor
    {
        $composerFile    = $this->env->package()->file('composer.json');
        $template        = new Template\ComposerJsonTemplate($composerFile);
        $compareComposer = new Processor\CompareFile($template, $composerFile);

        $comparePackage  = new Processor\SkeletonFilesProcessor($this->env->skeleton(), $fileValidators);

        return new Processor\ProcessorSequence($compareComposer, $comparePackage);
    }
}
