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
use Shudd3r\PackageFiles\Application\Token\Source\Data\SavedPlaceholderValues;


class TokenReaders
{
    private array $readerFactories;

    public function __construct(array $readerFactories)
    {
        $this->readerFactories = $readerFactories;
    }

    public function initializationReader(): Reader
    {
        $readers = [];
        foreach ($this->readerFactories as $name => $replacement) {
            $readers[$name] = $replacement->initializationReader();
        }

        return new Reader\CompositeTokenReader($readers);
    }

    public function validationReader(SavedPlaceholderValues $metaData): Reader
    {
        $readers = [];
        foreach ($this->readerFactories as $name => $replacement) {
            $source = new Source\MetaDataFile($name, $metaData, new Source\PredefinedValue(''));
            $readers[$name] = $replacement->validationReader($source);
        }

        return new Reader\CompositeTokenReader($readers);
    }

    public function updateReader(SavedPlaceholderValues $metaData): Reader
    {
        $readers = [];
        foreach ($this->readerFactories as $name => $replacement) {
            $source = new Source\MetaDataFile($name, $metaData, new Source\PredefinedValue(''));
            $readers[$name] = $replacement->updateReader($source);
        }

        return new Reader\CompositeTokenReader($readers);
    }
}
