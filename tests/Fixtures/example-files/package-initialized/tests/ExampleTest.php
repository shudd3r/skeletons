<?php declare(strict_types=1);

/*
 * This file is part of Initial/Package-name package.
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Package\Initial\Tests;

use PHPUnit\Framework\TestCase;
use Package\Initial\Example;


class ExampleTest extends TestCase
{
    public function testWelcomeMethodWithoutParameter_ReturnsDefaultWelcomeString()
    {
        $example = new Example();
        $this->assertSame('Hello World!', $example->welcome());
    }

    public function testWelcomeMethodWithUserName_ReturnsWelcomeStringWithUserName()
    {
        $example = new Example();
        $this->assertSame('Hello World!', $example->welcome());
    }
}
