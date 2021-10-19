<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Template\Factory;

use Shudd3r\PackageFiles\Application\Template\Factory;
use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Application\Template;


class MergedJsonFactory implements Factory
{
    public function template(string $filename, RuntimeEnv $env): Template
    {
        return new Template\MergedJsonTemplate(
            new Template\BasicTemplate($env->skeleton()->file($filename)->contents()),
            $env->package()->file($filename)->contents(),
            $env->metaDataFile()->exists()
        );
    }
}
