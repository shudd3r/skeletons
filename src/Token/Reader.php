<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Token;

use Shudd3r\PackageFiles\Token;
use Shudd3r\PackageFiles\Application\Output;
use Exception;


class Reader
{
    private Source $source;
    private Output $output;

    public function __construct(Source $source, Output $output)
    {
        $this->source = $source;
        $this->output = $output;
    }

    public function tokens(): Token
    {
        $createCallbacks = [
            fn() => new Repository($this->source->repositoryName()),
            fn() => new Package($this->source->packageName()),
            fn() => new Description($this->source->packageDescription()),
            fn() => new MainNamespace($this->source->sourceNamespace())
        ];

        $tokens     = [];
        $unresolved = false;
        foreach ($createCallbacks as $createCallback) {
            $token = $this->createToken($createCallback);
            if (!$token) { $unresolved = true; }
            $tokens[] = $token;
        }

        if ($unresolved) {
            throw new Exception('Cannot process unresolved properties');
        }

        return new TokenGroup(...$tokens);
    }

    private function createToken(callable $callback): ?Token
    {
        try {
            return $callback();
        } catch (Exception $e) {
            $this->output->send($e->getMessage(), 1);
            return null;
        }
    }
}
