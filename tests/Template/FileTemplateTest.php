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
            This file is part of {PACKAGE_NAME} package.
            {PACKAGE_DESC}.
            Repository: {REPO_NAME} URL: {REPO_URL}
            Source files namespace: {PACKAGE_NS}
            TPL;

        $template = new FileTemplate(new Doubles\MockedFile($contents));
        $properties = new Doubles\FakeProperties([
            'repositoryUrl'      => 'https://github.com/polymorphine/package.git',
            'packageName'        => 'polymorphine/dev',
            'packageDescription' => 'Package description',
            'sourceNamespace'    => 'Polymorphine\Dev'
        ]);

        $render = $template->render($properties);

        $expected = <<<'RENDER'
            This file is part of polymorphine/dev package.
            Package description.
            Repository: polymorphine/package URL: https://github.com/polymorphine/package.git
            Source files namespace: Polymorphine\Dev
            RENDER;

        $this->assertSame($expected, $render);
    }

    public function testNotExistingTemplateFile_ThrowsException()
    {
        $templateFile = new Doubles\MockedFile('', false);

        $this->expectException(InvalidArgumentException::class);
        new FileTemplate($templateFile);
    }
}