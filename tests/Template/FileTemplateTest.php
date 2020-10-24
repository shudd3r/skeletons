<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Template;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Template\FileTemplate;
use Shudd3r\PackageFiles\Tests\Doubles;
use InvalidArgumentException;


class FileTemplateTest extends TestCase
{
    public function testFileTokensAreReplacedByProperties()
    {
        $contents = <<<'TPL'
            This file is part of {package.name} package.
            TPL;

        $template = new FileTemplate(new Doubles\MockedFile($contents));
        $token    = Doubles\FakeToken::withPlaceholder('{package.name}', 'package/name');

        $render = $template->render($token);

        $expected = <<<'RENDER'
            This file is part of package/name package.
            RENDER;

        $this->assertSame($expected, $render);
    }

    public function testNotExistingTemplateFile_ThrowsException()
    {
        $templateFile = new Doubles\MockedFile(null);

        $this->expectException(InvalidArgumentException::class);
        new FileTemplate($templateFile);
    }
}
