<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Doubles;

use Shudd3r\Skeletons\Templates\Factory;
use Shudd3r\Skeletons\Environment\Files\File;
use Shudd3r\Skeletons\Templates\Template;


class FakeTemplateFactory implements Factory
{
    private Template $template;

    public function __construct(Template $template = null)
    {
        $this->template = $template ?? new Template\BasicTemplate('foo');
    }

    public function template(File $template, File $package): Template
    {
        return $this->template;
    }
}
