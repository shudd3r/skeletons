<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Processor;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Processor\FilesProcessor;
use Shudd3r\PackageFiles\Replacements\Token\ValueToken;
use Shudd3r\PackageFiles\Templates;
use Shudd3r\PackageFiles\Tests\Doubles;


class FilesProcessorTest extends TestCase
{
    public function testWithoutDefinedCustomTemplate_ProcessorUsesGenericTemplate()
    {
        $env        = $this->env();
        $processors = new Doubles\MockedProcessors();

        $processor  = new FilesProcessor($env->package(), new Templates($env, []), $processors);
        $processor->process(new valueToken('placeholder', 'value'));

        $expectedTemplate = $this->template($env->skeleton()->file('myFile.txt')->contents());
        $this->assertEquals(['myFile.txt' => $expectedTemplate], $processors->usedTemplates());
    }

    public function testWithDefinedCustomTemplate_ProcessorUsesThisTemplate()
    {
        $env        = $this->env();
        $template   = $this->template('render');
        $factories  = ['myFile.txt' => new Doubles\FakeTemplateFactory($template)];
        $templates  = new Templates($env, $factories);
        $processors = new Doubles\MockedProcessors();

        $processor = new FilesProcessor($env->package(), $templates, $processors);
        $processor->process(new valueToken('placeholder', 'value'));

        $this->assertSame(['myFile.txt' => $template], $processors->usedTemplates());
    }

    private function env(): Doubles\FakeRuntimeEnv
    {
        $env = new Doubles\FakeRuntimeEnv();

        $env->package()->addFile('myFile.txt', 'my file contents');
        $env->skeleton()->addFile('myFile.txt', 'template contents');

        return $env;
    }

    private function template(string $contents): Templates\Template\BasicTemplate
    {
        return new Templates\Template\BasicTemplate($contents);
    }
}
