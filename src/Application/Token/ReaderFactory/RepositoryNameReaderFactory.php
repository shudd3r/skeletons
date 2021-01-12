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


class RepositoryNameReaderFactory extends ValueReaderFactory
{
    protected ?string $inputPrompt = 'Github repository name';
    protected ?string $optionName  = 'repo';

    private PackageNameReaderFactory $packageName;

    public function __construct(RuntimeEnv $env, array $options, PackageNameReaderFactory $packageName)
    {
        $this->packageName = $packageName;
        parent::__construct($env, $options);
    }

    protected function defaultSource(): Source
    {
        /** @var Reader\PackageName $packageName */
        $packageName = $this->packageName->initializationReader();

        $config = $this->env->package()->file('.git/config');
        $source = new Source\DefaultRepositoryName($config, $packageName);
        return $this->userSource($source);
    }

    protected function newReaderInstance(Source $source): Reader
    {
        return new Reader\RepositoryName($source);
    }
}
