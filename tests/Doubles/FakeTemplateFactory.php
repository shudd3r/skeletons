<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Doubles;

use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Environment\FileSystem\File;


class FakeTemplateFactory implements Template\Factory
{
    private Template $template;

    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    public function template(File $skeletonFile): Template
    {
        return $this->template;
    }
}
