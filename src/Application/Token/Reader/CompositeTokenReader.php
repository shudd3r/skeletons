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


class CompositeTokenReader implements Reader
{
    /** @var Token[] */
    private array $readers;

    public function __construct(array $readers)
    {
        $this->readers = $readers;
    }

    public function token($namespace = ''): ?Token
    {
        if ($namespace) { $namespace .= '.'; }
        $create = fn (Reader $reader, string $name): ?Token => $reader->token($namespace . $name);
        $tokens = array_map($create, $this->readers, array_keys($this->readers));

        return array_filter($tokens) === $tokens ? new Token\CompositeToken(...$tokens) : null;
    }

    public function value(): string
    {
        $values = [];
        foreach ($this->readers as $reader) {
            $values[get_class($reader)] = $reader->value();
        }

        return json_encode($values, JSON_PRETTY_PRINT);
    }
}
