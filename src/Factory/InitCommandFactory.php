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
use Shudd3r\PackageFiles\CommandHandler;
use Shudd3r\PackageFiles\Properties\Reader;
use Shudd3r\PackageFiles\Properties\Source;
use Shudd3r\PackageFiles\Subroutine;
use Shudd3r\PackageFiles\Application\Command;
use Shudd3r\PackageFiles\Template;


class InitCommandFactory extends Factory
{
    public function command(array $options): Command
    {
        $subroutine = new Subroutine\SubroutineSequence($this->generateComposer(), $this->generateMetaFile());
        $subroutine = new Subroutine\ValidateProperties($this->env->output(), $subroutine);

        return new CommandHandler(new Reader($this->source($options)), $subroutine);
    }

    private function generateComposer(): Subroutine
    {
        $composerFile = $this->env->packageFiles()->file('composer.json');
        $template     = new Template\ComposerJsonTemplate($composerFile);

        return new Subroutine\GenerateFile($template, $composerFile);
    }

    private function generateMetaFile(): Subroutine
    {
        $templateFile = $this->env->skeletonFiles()->file('package.properties');
        $metaDataFile = $this->env->packageFiles()->file('.github/package.properties');
        $template     = new Template\FileTemplate($templateFile);

        return new Subroutine\GenerateFile($template, $metaDataFile);
    }

    private function source(array $options): Source
    {
        $source = new Source\PackageConfigFiles($this->env->packageFiles());
        $source = new Source\CommandLineOptions($options, $source);
        $source = new Source\DirectoryStructureFallback($source, $this->env->packageFiles());
        if (isset($options['i']) || isset($options['interactive'])) {
            $source = new Source\InteractiveInput($this->env->input(), $source);
        }

        return $source;
    }
}
