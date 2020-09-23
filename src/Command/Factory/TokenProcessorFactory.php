<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Command\Factory;

use Shudd3r\PackageFiles\Command\Factory;
use Shudd3r\PackageFiles\Command\TokenProcessor;
use Shudd3r\PackageFiles\Application\Command;
use Shudd3r\PackageFiles\RuntimeEnv;
use Shudd3r\PackageFiles\Token\Reader;
use Shudd3r\PackageFiles\Processor;


abstract class TokenProcessorFactory implements Factory
{
    protected RuntimeEnv $env;
    protected array      $options;

    public function __construct(RuntimeEnv $env, array $options)
    {
        $this->env     = $env;
        $this->options = $options;
    }

    public function command(): Command
    {
        $reader = new Reader\TokensReader($this->env->output(), ...$this->tokenReaders());
        return new TokenProcessor($reader, $this->processor());
    }

    /**
     * @return Reader[]
     */
    abstract protected function tokenReaders(): array;

    abstract protected function processor(): Processor;
}
