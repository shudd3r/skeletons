<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token\ReaderFactory;

use Shudd3r\PackageFiles\Application\Token\ReaderFactory;
use Shudd3r\PackageFiles\Application\Token\Reader;
use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Application\RuntimeEnv;


class PackageDescriptionReaderFactory implements ReaderFactory
{
    private RuntimeEnv         $env;
    private array              $options;
    private Reader\PackageName $packageName;

    public function __construct(RuntimeEnv $env, array $options, Reader\PackageName $packageName)
    {
        $this->env         = $env;
        $this->options     = $options;
        $this->packageName = $packageName;
    }

    public function initializationReader(): Reader
    {
        $composer = new Source\Data\ComposerJsonData($this->env->package()->file('composer.json'));
        $source   = new Source\DefaultPackageDescription($composer, $this->packageName);
        return $this->readerInstance($this->userSource($source));
    }

    public function validationReader(): Reader
    {
        $source = new Source\MetaDataFile($this->env->metaDataFile(), new Source\PredefinedValue(''));
        return $this->readerInstance($source);
    }

    public function updateReader(): Reader
    {
        $source = new Source\MetaDataFile($this->env->metaDataFile(), new Source\PredefinedValue(''));
        return $this->readerInstance($this->userSource($source));
    }

    private function readerInstance(Source $source): Reader
    {
        return new Reader\PackageDescription($source);
    }

    private function userSource(Source $defaultSource): Source
    {
        return $this->interactive('Package description', $this->option('desc', $defaultSource));
    }

    private function option(string $name, Source $default): Source
    {
        return isset($this->options[$name])
            ? new Source\PredefinedValue($this->options[$name])
            : $default;
    }

    private function interactive(string $prompt, Source $source): Source
    {
        return isset($this->options['i']) || isset($this->options['interactive'])
            ? new Source\InteractiveInput($prompt, $this->env->input(), $source)
            : $source;
    }
}
