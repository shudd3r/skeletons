<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\FileSystem;

use Shudd3r\PackageFiles\Application\FileSystem;


trait LocalFileSystemMethods
{
    protected static string $root;

    public static function setUpBeforeClass(): void
    {
        self::$root = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tests';
        mkdir(self::$root);
    }

    public static function tearDownAfterClass(): void
    {
        if (!is_dir(self::$root)) { return; }
        $message = 'Temporary directory (' . self::$root . ') state was not restored after tests';
        trigger_error($message, E_USER_WARNING);
        self::remove();
    }

    private static function files(array $names): array
    {
        $createFile = fn($filename) => self::directory()->file($filename);
        return array_map($createFile, $names);
    }

    private static function directories(array $names): array
    {
        $createDirectory = fn($dirname) => self::directory(self::$root . DIRECTORY_SEPARATOR . $dirname);
        return array_map($createDirectory, $names);
    }

    private static function file(string $filename): FileSystem\File
    {
        return new FileSystem\File\LocalFile($filename);
    }

    private static function directory(string $dirname = null): FileSystem\Directory
    {
        return new FileSystem\Directory\LocalDirectory($dirname ?? self::$root);
    }

    private static function create(string $path): void
    {
        $segments = explode(DIRECTORY_SEPARATOR, str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path));
        $basename = array_pop($segments);

        $path = self::$root;
        if (!is_dir($path)) { mkdir($path); }

        foreach ($segments as $segment) {
            $path .= DIRECTORY_SEPARATOR . $segment;
            if (is_dir($path)) { continue; }
            mkdir($path);
        }

        $path .= DIRECTORY_SEPARATOR . $basename;
        file_put_contents($path, 'x');
    }

    private static function remove(string $name = null): void
    {
        $path = $name ? self::$root . DIRECTORY_SEPARATOR . $name : self::$root;
        if (is_file($path) && unlink($path)) { return; }

        $elements = array_diff(scandir($path), ['.', '..']);
        array_map(fn($element) => self::remove($name . DIRECTORY_SEPARATOR . $element), $elements);
        rmdir($path);
    }
}
