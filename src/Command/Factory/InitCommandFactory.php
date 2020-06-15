<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Command\Factory;

use Shudd3r\PackageFiles\Command;
use Shudd3r\PackageFiles\Command\Factory;
use Shudd3r\PackageFiles\Command\Subroutine;
use Shudd3r\PackageFiles\RuntimeEnv;
use Shudd3r\PackageFiles\Properties\Reader\InitialPropertiesReader;
use Shudd3r\PackageFiles\Template;


class InitCommandFactory implements Factory
{
    public function command(RuntimeEnv $env): Command
    {
        $subroutine = new Subroutine\SubroutineSequence($this->generateComposer($env), $this->generateMetaFile($env));
        $subroutine = new Subroutine\ValidateProperties($env->output(), $subroutine);

        return new Command(new InitialPropertiesReader($env), $subroutine);
    }

    public function generateComposer(RuntimeEnv $env): Subroutine
    {
        $composerFile = $env->packageFiles()->file('composer.json');
        $template     = new Template\ComposerJsonTemplate($composerFile);

        return new Subroutine\GenerateFile($template, $composerFile);
    }

    public function generateMetaFile(RuntimeEnv $env): Subroutine
    {
        $templateFile = $env->skeletonFiles()->file('package.properties');
        $metaDataFile = $env->packageFiles()->file('.github/package.properties');
        $template     = new Template\FileTemplate($templateFile);

        return new Subroutine\GenerateFile($template, $metaDataFile);
    }
}
