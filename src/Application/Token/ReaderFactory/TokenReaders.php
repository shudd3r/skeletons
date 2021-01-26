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


class TokenReaders
{
    private $createMethod;
    private array $readerFactories;

    /**
     * @param callable $createMethod    fn(string, ReaderFactory) => Reader
     * @param array    $readerFactories
     */
    public function __construct(callable $createMethod, array $readerFactories)
    {
        $this->createMethod    = $createMethod;
        $this->readerFactories = $readerFactories;
    }

    public function reader(): Reader
    {
        $readers = [];
        foreach ($this->readerFactories as $name => $replacement) {
            $readers[$name] = ($this->createMethod)($name, $replacement);
        }

        return new Reader\CompositeTokenReader($readers);
    }
}
