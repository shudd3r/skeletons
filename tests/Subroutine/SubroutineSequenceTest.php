<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Subroutine;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Subroutine;
use Shudd3r\PackageFiles\Token;
use Shudd3r\PackageFiles\Tests\Doubles;


class SubroutineSequenceTest extends TestCase
{
    public function testAllSubroutinesAreCalled()
    {
        $subroutines = [
            new Doubles\MockedSubroutine(),
            new Doubles\MockedSubroutine(),
            new Doubles\MockedSubroutine(),
            new Doubles\MockedSubroutine()
        ];

        $sequence = new Subroutine\SubroutineSequence(...$subroutines);
        $this->assertSubroutineCalled(null, ...$subroutines);

        $sequence->process($tokens = new Doubles\FakeToken());
        $this->assertSubroutineCalled($tokens, ...$subroutines);
    }

    private function assertSubroutineCalled(?Token $token, Doubles\MockedSubroutine ...$subroutines): void
    {
        foreach ($subroutines as $subroutine) {
            $this->assertSame($token, $subroutine->passedToken);
        }
    }
}
