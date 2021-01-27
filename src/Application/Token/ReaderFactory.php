<?php

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token;

use Shudd3r\PackageFiles\Application\Token\Reader\ValueReader;


interface ReaderFactory
{
    public function initializationReader(): ValueReader;

    public function validationReader(string $namespace): ValueReader;

    public function updateReader(string $namespace): ValueReader;
}
