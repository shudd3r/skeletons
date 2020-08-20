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
use Shudd3r\PackageFiles\Token\Reader;
use Shudd3r\PackageFiles\Subroutine;
use Shudd3r\PackageFiles\Template;


class InitCommandFactory extends Factory
{
    protected function tokenReaders(): array
    {
        $files    = $this->env->packageFiles();
        $composer = new Reader\Data\ComposerJsonData($files->file('composer.json'));

        return [
            $package = $this->cached($this->interactive(new Reader\PackageReader($composer, $files))),
            $this->interactive(new Reader\RepositoryReader($files->file('.git/config'), $package)),
            $this->interactive(new Reader\DescriptionReader($composer, $package)),
            $this->interactive(new Reader\NamespaceReader($composer, $package))
        ];
    }

    protected function subroutine(): Subroutine
    {
        $packageFiles = $this->env->packageFiles();

        $composerFile     = $packageFiles->file('composer.json');
        $template         = new Template\ComposerJsonTemplate($composerFile);
        $generateComposer = new Subroutine\GenerateFile($template, $composerFile);

        $templateFile     = $this->env->skeletonFiles()->file('package.properties');
        $metaDataFile     = $packageFiles->file('.github/package.properties');
        $template         = new Template\FileTemplate($templateFile);
        $generateMetaFile = new Subroutine\GenerateFile($template, $metaDataFile);

        return new Subroutine\SubroutineSequence($generateComposer, $generateMetaFile);
    }

    private function cached(Reader\ValueReader $reader): Reader\ValueReader
    {
        return new Reader\CachedValueReader($reader);
    }

    private function interactive(Reader\ValueReader $reader, bool $commandLine = true): Reader\ValueReader
    {
        if ($commandLine) {
            $reader = new Reader\CommandOptionReader($this->options, $reader);
        }

        return isset($this->options['i']) || isset($this->options['interactive'])
            ? new Reader\InputReader($this->env->input(), $reader)
            : $reader;
    }
}
