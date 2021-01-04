<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token\Source;

use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Application\Token\Validator;


class PredefinedValue implements Source
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function value(Validator $validator): string
    {
        return $this->value;
    }
}
