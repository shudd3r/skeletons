<?php declare(strict_types=1);

/*
* This file is part of Shudd3r/Skeletons package.
*
* (c) Shudd3r <q3.shudder@gmail.com>
*
* This source file is subject to the MIT license that is bundled
* with this source code in the file LICENSE.
*/

namespace Shudd3r\Skeletons\Tests\Environment\Files;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Tests\Doubles\FakePath;


class PathNormalizationTest extends TestCase
{
    public function test_for_relative_paths_separators_are_trimmed_on_both_ends(): void
    {
        $path = new FakePath('/foo/bar\\baz\\');
        $this->assertSame('foo/bar/baz', $path->normalized());

        $path = new FakePath('/foo/bar\\baz\\', '\\');
        $this->assertSame('foo\\bar\\baz', $path->normalized());
    }

    public function test_for_absolute_paths_only_trailing_separators_are_trimmed(): void
    {
        $path = new FakePath('/foo/bar\\baz\\', '/', true);
        $this->assertSame('/foo/bar/baz', $path->normalized());

        $path = new FakePath('/foo/bar\\baz\\', '\\', true);
        $this->assertSame('\\foo\\bar\\baz', $path->normalized());
    }

    public function test_windows_paths_with_scheme_prefix_are_normalized_correctly(): void
    {
        $path = new FakePath('phar://D:\some\path\foo.phar/bar', '\\', true);
        $this->assertSame('phar://D:\\some\\path\\foo.phar\\bar', $path->normalized());
    }
}
