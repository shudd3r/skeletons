<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Token\Source\Decorator;

use Shudd3r\PackageFiles\Token\Source;
use Shudd3r\PackageFiles\Application\Output;
use Shudd3r\PackageFiles\Token;


class ErrorMessageOutput implements Source
{
    private Source $source;
    private Output $output;
    private string $tokenName;

    public function __construct(Source $source, Output $output, string $tokenName)
    {
        $this->source    = $source;
        $this->output    = $output;
        $this->tokenName = $tokenName;
    }
    public function create(string $value): ?Token
    {
        $token = $this->source->create($value);
        if ($token) { return $token; }

        $this->output->send("Invalid {$this->tokenName} value: `{$value}`");
        return null;
    }

    public function value(): string
    {
        return $this->source->value();
    }
}
