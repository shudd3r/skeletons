<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles;

use Shudd3r\PackageFiles\Application\Command;


abstract class Factory
{
    protected RuntimeEnv $env;

    public function __construct(RuntimeEnv $env)
    {
        $this->env = $env;
    }

    public function command(array $options): Command
    {
        $reader = new Token\Reader($this->env->output(), ...$this->tokenCallbacks($options));
        return new CommandHandler($reader, $this->subroutine($options));
    }

    /**
     * @param array $options
     *
     * @return callable[] fn() => Token
     */
    abstract protected function tokenCallbacks(array $options): array;

    abstract protected function subroutine(array $options): Subroutine;
}
