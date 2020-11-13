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
use Shudd3r\PackageFiles\Token;
use Shudd3r\PackageFiles\Application\Output;
use Exception;


abstract class ValueReader implements Reader, Source
{
    private Source  $source;
    private Output  $output;
    private ?string $value = null;

    public function __construct(Source $source, Output $output)
    {
        $this->source = $source;
        $this->output = $output;
    }

    public function token(): ?Token
    {
        try {
            return $this->createToken($this->value());
        } catch (Exception $e) {
            $this->output->send($e->getMessage(), 1);
            return null;
        }
    }

    public function value(): string
    {
        return $this->value ??= $this->source->value();
    }

    abstract protected function createToken(string $value): Token;
}
