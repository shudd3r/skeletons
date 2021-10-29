<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Templates\Factory;

use Shudd3r\Skeletons\Templates\Factory;
use Shudd3r\Skeletons\Templates\Template;
use Shudd3r\Skeletons\RuntimeEnv;


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
