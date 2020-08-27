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

        $package = new Reader\PackageReader($composer, $files);
        $package = $this->interactive('Packagist package name', $this->commandOption('package', $package));
        $package = $this->cached($package);

        $repo = new Reader\RepositoryReader($files->file('.git/config'), $package);
        $repo = $this->interactive('Github repository name', $this->commandOption('repo', $repo));

        $desc = new Reader\DescriptionReader($composer, $package);
        $desc = $this->interactive('Package description', $this->commandOption('desc', $desc));

        $namespace = new Reader\NamespaceReader($composer, $package);
        $namespace = $this->interactive('Source files namespace', $this->commandOption('ns', $namespace));

        return [$package, $repo, $desc, $namespace];
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

    private function commandOption(string $option, Reader\ValueReader $reader): Reader\ValueReader
    {
        return isset($this->options[$option])
            ? new Reader\CommandOptionReader($this->options[$option], $reader)
            : $reader;
    }

    private function interactive(string $prompt, Reader\ValueReader $reader): Reader\ValueReader
    {
        return isset($this->options['i']) || isset($this->options['interactive'])
            ? new Reader\InputReader($prompt, $this->env->input(), $reader)
            : $reader;
    }
}
