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

use Shudd3r\PackageFiles\Application\Token\Reader;
use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Application\RuntimeEnv;


class SrcNamespaceReaderFactory extends ValueReaderFactory
{
    protected ?string $inputPrompt = 'Source files namespace';
    protected ?string $optionName  = 'ns';

    private PackageNameReaderFactory $packageName;

    public function __construct(RuntimeEnv $env, array $options, PackageNameReaderFactory $packageName)
    {
        $this->packageName = $packageName;
        parent::__construct($env, $options);
    }

    public function isValid(string $value): bool
    {
        foreach (explode('\\', $value) as $label) {
            $isValidLabel = (bool) preg_match('#^[a-z_\x7f-\xff][a-z0-9_\x7f-\xff]*$#Di', $label);
            if (!$isValidLabel) { return false; }
        }

        return true;
    }

    protected function defaultSource(): Source
    {
        /** @var Reader\PackageName $packageName */
        $packageName = $this->packageName->initializationReader();

        $composer = new Source\Data\ComposerJsonData($this->env->package()->file('composer.json'));
        $source   = new Source\DefaultSrcNamespace($composer, $packageName);
        return $this->userSource($source);
    }

    protected function newReaderInstance(Source $source): Reader
    {
        return new Reader\SrcNamespace($this, $source);
    }
}
