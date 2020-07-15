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


class Description implements Token
{
    public const TEXT  = '{description.text}';

    private string $description;

    public function __construct(string $description)
    {
        if (!$description) {
            throw new Exception("Empty package description");
        }

        $this->description = $description;
    }

    public function replacePlaceholders(string $template): string
    {
        return str_replace(self::TEXT, $this->description, $template);
    }
}
