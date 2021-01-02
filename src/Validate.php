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

    protected function tokenReaders(): array
    {
        $files    = $this->env->package();
        $composer = new Reader\Data\ComposerJsonData($files->file('composer.json'));
        $metaFile = $this->env->metaDataFile();
        $source   = new Source\MetaDataFile($metaFile, new Source\PredefinedValue(''));

        return [
            $package = new Reader\PackageName($composer, $files, $source),
            new Reader\RepositoryName($files->file('.git/config'), $package, $source),
            new Reader\PackageDescription($composer, $package, $source),
            new Reader\SrcNamespace($composer, $package, $source)
        ];
    }

    protected function processor(): Processor
    {
        $composerFile    = $this->env->package()->file('composer.json');
        $template        = new Template\ComposerJsonTemplate($composerFile);
        $compareComposer = new Processor\CompareFile($template, $composerFile);

        $generatorFactory = new Processor\Factory\FileValidatorFactory($this->env->package());
        $comparePackage   = new Processor\SkeletonFilesProcessor($this->env->skeleton(), $generatorFactory);

        return new Processor\ProcessorSequence($compareComposer, $comparePackage);
    }
}
