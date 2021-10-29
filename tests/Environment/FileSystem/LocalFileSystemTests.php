<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Environment\FileSystem;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Environment\FileSystem;


class LocalFileSystemTests extends TestCase
{
    protected static string $root;

    public static function setUpBeforeClass(): void
    {
        self::$root = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tests';
        mkdir(self::$root);
    }

    public static function tearDownAfterClass(): void
    {
        rmdir(self::$root);
    }

    protected static function files(array $names, string $subdirectory = ''): array
    {
        return array_map(fn($filename) => self::file($filename, $subdirectory), $names);
    }

    protected static function directories(array $names): array
    {
        return array_map(fn($dirname) => self::directory($dirname), $names);
    }

    protected static function file(string $filename, string $subdirectory = ''): FileSystem\File
    {
        return new FileSystem\File\LocalFile(self::directory($subdirectory), $filename);
    }

    protected static function directory(string $dirname = '', bool $absolute = false): FileSystem\Directory
    {
        $path = $absolute ? $dirname : self::$root . DIRECTORY_SEPARATOR . $dirname;
        return new FileSystem\Directory\LocalDirectory($path);
    }

    protected static function create(string $path, string $contents = 'x'): void
    {
        $segments = explode(DIRECTORY_SEPARATOR, str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path));
        $basename = array_pop($segments);

        $path = self::$root;
        foreach ($segments as $segment) {
            $path .= DIRECTORY_SEPARATOR . $segment;
            if (is_dir($path)) { continue; }
            mkdir($path);
        }

        file_put_contents($path . DIRECTORY_SEPARATOR . $basename, $contents);
    }

    protected static function clear(string $name = ''): void
    {
        $path = $name ? self::$root . DIRECTORY_SEPARATOR . $name : self::$root;
        if (is_file($path) && unlink($path)) { return; }

        $elements = array_diff(scandir($path), ['.', '..']);
        array_map(fn($element) => self::clear($name . DIRECTORY_SEPARATOR . $element), $elements);
        if ($name) { rmdir($path); }
    }
}
