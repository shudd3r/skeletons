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
use Shudd3r\PackageFiles\Token\Reader\Source;
use Shudd3r\PackageFiles\Application\FileSystem\Directory;
use Shudd3r\PackageFiles\Template;


class InitCommandFactory extends Factory
{
    protected function tokenCallbacks(): array
    {
        $files    = $this->env->packageFiles();
        $composer = new Token\Reader\Data\ComposerJsonData($files->file('composer.json'));

        $package = new Source\CachedSource($this->interactive(
            'Packagist package name',
            new Source\PrioritySearch(
                $this->commandLine('package'),
                new Source\CallbackSource(fn() => $composer->value('name') ?? ''),
                new Source\CallbackSource(fn() => $this->directoryFallback($files))
            )
        ));

        $input = new Token\Reader\Data\UserInputData($this->options, $this->env->input());

        $repository  = new Token\Reader\RepositoryReader($input, $files->file('.git/config'), $package);
        $description = new Token\Reader\DescriptionReader($input, $composer, $package);
        $namespace   = new Token\Reader\NamespaceReader($input, $composer, $package);

        return [
            fn() => $repository->token(),
            fn() => new Token\Package($package->value()),
            fn() => $description->token(),
            fn() => $namespace->token()
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

    private function interactive(string $prompt, Source $source): Source
    {
        return isset($this->options['i']) || isset($this->options['interactive'])
            ? new Source\InteractiveInput($prompt, $this->env->input(), $source)
            : $source;
    }

    private function commandLine(string $option): Source
    {
        return new Source\CallbackSource(fn() => $this->options[$option] ?? '');
    }

    private function directoryFallback(Directory $files): string
    {
        $path = $files->path();
        return $path ? basename(dirname($path)) . '/' . basename($path) : '';
    }
}
