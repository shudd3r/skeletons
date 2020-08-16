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
use Shudd3r\PackageFiles\Token;
use Shudd3r\PackageFiles\Subroutine;
use Shudd3r\PackageFiles\Template;


class InitCommandFactory extends Factory
{
    protected function tokenReaders(): array
    {
        $files    = $this->env->packageFiles();
        $input    = new Token\Reader\Data\UserInputData($this->options, $this->env->input());
        $composer = new Token\Reader\Data\ComposerJsonData($files->file('composer.json'));

        $package     = new Token\Reader\PackageReader($input, $composer, $files);
        $repository  = new Token\Reader\RepositoryReader($input, $files->file('.git/config'), $package);
        $description = new Token\Reader\DescriptionReader($input, $composer, $package);
        $namespace   = new Token\Reader\NamespaceReader($input, $composer, $package);

        return [$package, $repository, $description, $namespace];
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
}
