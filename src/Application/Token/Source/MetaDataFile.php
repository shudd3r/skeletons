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
use Shudd3r\PackageFiles\Application\Token\Source\Data\SavedPlaceholderValues;
use Shudd3r\PackageFiles\Application\Token\Validator;


class MetaDataFile implements Source
{
    private string                 $name;
    private SavedPlaceholderValues $metaData;
    private Source                 $fallback;

    public function __construct(string $name, SavedPlaceholderValues $metaData, Source $fallback)
    {
        $this->name     = $name;
        $this->metaData = $metaData;
        $this->fallback = $fallback;
    }

    public function value(Validator $validator): string
    {
        return $this->metaData->value($this->name) ?? $this->fallback->value($validator);
    }
}
