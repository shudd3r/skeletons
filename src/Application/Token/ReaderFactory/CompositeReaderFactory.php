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


class CompositeReaderFactory implements ReaderFactory
{
    private array $readerFactories;

    public function __construct(array $readerFactories)
    {
        $this->readerFactories = $readerFactories;
    }

    public function initializationReader(): Reader
    {
        return $this->readerInstance(fn(ReaderFactory $factory) => $factory->initializationReader());
    }

    public function validationReader(): Reader
    {
        return $this->readerInstance(fn(ReaderFactory $factory) => $factory->validationReader());
    }

    public function updateReader(): Reader
    {
        return $this->readerInstance(fn(ReaderFactory $factory) => $factory->updateReader());
    }

    private function readerInstance(callable $createComponent): Reader
    {
        $readers = [];
        foreach ($this->readerFactories as $name => $replacement) {
            $readers[$name] = $createComponent($replacement);
        }

        return new Reader\CompositeTokenReader($readers);
    }
}
