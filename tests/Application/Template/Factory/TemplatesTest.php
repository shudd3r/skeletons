<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Template\Factory;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Exception\TemplateOverwriteException;
use Shudd3r\PackageFiles\Application\Template\BasicTemplate;
use Shudd3r\PackageFiles\Application\Template\Factory\Templates;
use Shudd3r\PackageFiles\Tests\Doubles\FakeTemplateFactory;


class TemplatesTest extends TestCase
{
    public function testOverwritingTemplateForDefinedFile_ThrowsException()
    {
        $templates = new Templates();
        $templates->add('file.txt', new FakeTemplateFactory(new BasicTemplate('')));

        $this->expectException(TemplateOverwriteException::class);
        $templates->add('file.txt', new FakeTemplateFactory(new BasicTemplate('')));
    }
}
