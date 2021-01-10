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


class CachedReaderFactory implements ReaderFactory
{
    private ReaderFactory $factory;

    private Reader $initializeReader;
    private Reader $validationReader;
    private Reader $updateReader;

    public function __construct(ReaderFactory $factory)
    {
        $this->factory = $factory;
    }

    public function initializationReader(): Reader
    {
        return $this->initializeReader ??= $this->factory->initializationReader();
    }

    public function validationReader(Source $metaDataSource): Reader
    {
        return $this->validationReader ??= $this->factory->validationReader($metaDataSource);
    }

    public function updateReader(Source $metaDataSource): Reader
    {
        return $this->updateReader ??= $this->factory->updateReader($metaDataSource);
    }
}
