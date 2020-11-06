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
use Exception;


class MainNamespace implements Token
{
    public const SRC     = '{namespace.src}';
    public const SRC_ESC = '{namespace.src.esc}';

    private string $namespace;

    public function __construct(string $namespace)
    {
        foreach (explode('\\', $namespace) as $label) {
            if (!preg_match('#^[a-z_\x7f-\xff][a-z0-9_\x7f-\xff]*$#Di', $label)) {
                throw new Exception("Invalid label `{$label}` in `{$namespace}` namespace");
            }
        }

        $this->namespace = $namespace;
    }

    public function replacePlaceholders(string $template): string
    {
        $template = str_replace(self::SRC_ESC, str_replace('\\', '\\\\', $this->namespace), $template);
        return str_replace(self::SRC, $this->namespace, $template);
    }
}
