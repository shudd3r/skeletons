<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Token\Reader;

use Shudd3r\PackageFiles\Token\Reader;
use Shudd3r\PackageFiles\Application\Output;
use Shudd3r\PackageFiles\Token;


class TokensReader implements Reader
{
    private Output $output;
    private array  $readers;

    public function __construct(Output $output, Reader ...$readers)
    {
        $this->output  = $output;
        $this->readers = $readers;
    }

    public function token(): ?Token
    {
        $tokens     = [];
        $unresolved = false;
        foreach ($this->readers as $reader) {
            if (!$token = $reader->token()) {
                $unresolved = true;
                continue;
            }
            $tokens[] = $token;
        }

        if ($unresolved) {
            $this->output->send('Cannot process unresolved tokens', 1);
            return null;
        }

        return new Token\CompositeToken(...$tokens);
    }
}
