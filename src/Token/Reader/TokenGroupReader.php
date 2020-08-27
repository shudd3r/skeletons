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
use Exception;


class TokenGroupReader implements Reader
{
    private Output $output;
    private array  $readers;

    public function __construct(Output $output, Reader ...$readers)
    {
        $this->output  = $output;
        $this->readers = $readers;
    }

    public function token(): Token
    {
        $tokens     = [];
        $unresolved = false;
        foreach ($this->readers as $reader) {
            $token = $this->createToken($reader);
            if (!$token) { $unresolved = true; }
            $tokens[] = $token;
        }

        if ($unresolved) {
            throw new Exception('Cannot process unresolved tokens');
        }

        return new Token\TokenGroup(...$tokens);
    }

    private function createToken(Reader $reader): ?Token
    {
        try {
            return $reader->token();
        } catch (Exception $e) {
            $this->output->send($e->getMessage(), 1);
            return null;
        }
    }
}
