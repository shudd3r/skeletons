<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token;

use Shudd3r\PackageFiles\Application\RuntimeEnv;


class Replacements
{
    private RuntimeEnv $env;
    private array      $replacements = [];

    public function __construct(RuntimeEnv $env)
    {
        $this->env = $env;
    }

    public function add(string $name, Replacement $replacement): void
    {
        $this->replacements[$name] = $replacement;
    }

    public function init(array $options): Reader
    {
        return new Reader\InitialReader($this->replacements, $options);
    }

    public function validate(): Reader
    {
        return new Reader\ValidationReader($this->replacements);
    }

    public function update(array $options): Reader
    {
        return new Reader\UpdateReader($this->replacements, $options);
    }
}
