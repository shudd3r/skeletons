<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Processor;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Application\Replacements\Token\ValueToken;
use Shudd3r\PackageFiles\Tests\Doubles;


class FilesProcessorTest extends TestCase
{
    public function testWithoutDefinedCustomTemplate_ProcessorUsesGenericTemplate()
    {
        $env       = $this->env();
        $processor = new Doubles\MockedFilesProcessor($env->package(), new Template\Templates($env, []));
        $processor->process(new valueToken('placeholder', 'value'));

        $expected = ['myFile.txt' => new Template\BasicTemplate($env->skeleton()->file('myFile.txt')->contents())];
        $this->assertEquals($expected, $processor->usedTemplates());
    }

    public function testWithDefinedCustomTemplate_ProcessorUsesThisTemplate()
    {
        $env        = $this->env();
        $template   = new Template\BasicTemplate('render');
        $factories  = ['myFile.txt' => new Doubles\FakeTemplateFactory($template)];
        $templates  = new Template\Templates($env, $factories);

        $processor = new Doubles\MockedFilesProcessor($env->package(), $templates);
        $processor->process(new valueToken('placeholder', 'value'));

        $expected = ['myFile.txt' => $template];
        $this->assertSame($expected, $processor->usedTemplates());
    }

    private function env(): Doubles\FakeRuntimeEnv
    {
        $env = new Doubles\FakeRuntimeEnv();

        $env->package()->addFile('myFile.txt', 'my file contents');
        $env->skeleton()->addFile('myFile.txt', 'template contents');

        return $env;
    }
}
