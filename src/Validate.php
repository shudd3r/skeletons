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
        $tokenReader    = new Reader\CompositeTokenReader(...$this->tokenReaders());
        $processTokens  = new Command\TokenProcessor($tokenReader, $this->processor(), $this->env->output());

        return new Command\ProtectedCommand($processTokens, $metaDataExists);
    }

    public function synchronizedSkeleton(TokenCache $cache = null): Command\Precondition
    {
        $tokenReader = new Reader\CompositeTokenReader(...$this->tokenReaders());
        return new Command\Precondition\SkeletonSynchronization($tokenReader, $this->processor($cache));
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

    protected function processor(TokenCache $cache = null): Processor
    {
        $composerFile    = $this->env->package()->file('composer.json');
        $template        = new Template\ComposerJsonTemplate($composerFile);
        $compareComposer = new Processor\CompareFile($template, $composerFile);

        $validatorFactory = new Processor\Factory\FileValidators($this->env->package(), $cache);
        $comparePackage   = new Processor\SkeletonFilesProcessor($this->env->skeleton(), $validatorFactory);

        return new Processor\ProcessorSequence($compareComposer, $comparePackage);
    }
}
