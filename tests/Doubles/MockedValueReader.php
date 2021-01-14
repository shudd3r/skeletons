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

use Shudd3r\PackageFiles\Application\Token\Reader\ValueReader;


class MockedValueReader extends ValueReader
{
    public ?FakeSource $source;

    public function __construct(?string $sourceValue = 'foo', bool $valid = true)
    {
        $this->source = isset($sourceValue) ? new FakeSource($sourceValue) : null;
        parent::__construct(new FakeValidator($valid), $this->source);
    }
}
