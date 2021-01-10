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


class PackageNameReaderFactory implements ReaderFactory
{
    private RuntimeEnv $env;
    private array      $options;

    public function __construct(RuntimeEnv $env, array $options)
    {
        $this->env     = $env;
        $this->options = $options;
    }

    public function initializationReader(): Reader
    {
        $composer = new Source\Data\ComposerJsonData($this->env->package()->file('composer.json'));
        $source   = new Source\DefaultPackageName($composer, $this->env->package());
        return $this->readerInstance($this->userSource($source));
    }

    public function validationReader(Source $metaDataSource): Reader
    {
        return $this->readerInstance($metaDataSource);
    }

    public function updateReader(Source $metaDataSource): Reader
    {
        return $this->readerInstance($this->userSource($metaDataSource));
    }

    private function readerInstance(Source $source): Reader
    {
        return new Reader\PackageName($source);
    }

    private function userSource(Source $defaultSource): Source
    {
        return $this->interactive('Packagist package name', $this->option('package', $defaultSource));
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
