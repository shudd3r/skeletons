<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token\Reader;

use Shudd3r\PackageFiles\Application\Token\Reader;
use Shudd3r\PackageFiles\Application\Token;
use Shudd3r\PackageFiles\ReplacementReader;


class InitialReader extends Reader
{
    private array $options;

    public function __construct(array $replacements, array $options)
    {
        $this->options = $options;
        parent::__construct($replacements);
    }

    protected function tokenInstance(string $name, ReplacementReader $replacement): ?Token
    {
        return $replacement->initialToken($name, $this->options);
    }
}
