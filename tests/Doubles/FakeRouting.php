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

use Shudd3r\PackageFiles\Application\Routing;
use Shudd3r\PackageFiles\Application\Command;
use RuntimeException;


class FakeRouting implements Routing
{
    public const VALID_COMMAND     = 'command';
    public const EXCEPTION_MESSAGE = 'Unknown command';

    public ?FakeCommand $command;

    public function __construct(?FakeCommand $command = null)
    {
        $this->command = $command;
    }

    public function command(string $command): Command
    {
        if ($command !== self::VALID_COMMAND) {
            throw new RuntimeException(self::EXCEPTION_MESSAGE);
        }

        return $this->command ?? new FakeCommand();
    }
}
