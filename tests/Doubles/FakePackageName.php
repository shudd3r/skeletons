<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Doubles;

use Shudd3r\PackageFiles\Token\Reader\PackageName;
use Shudd3r\PackageFiles\Token\Source\Data\ComposerJsonData;


class FakePackageName extends PackageName
{
    public function __construct(string $value = 'package/name')
    {
        $composer  = new ComposerJsonData(new MockedFile());
        $directory = new FakeDirectory();
        $source    = new FakeSourceV2($value);

        parent::__construct($composer, $directory, $source);
    }
}
