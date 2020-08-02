<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Token\Reader\Source;

use Shudd3r\PackageFiles\Token\Reader\Source;
use Shudd3r\PackageFiles\Application\FileSystem\Directory;


class GitConfigRepository implements Source
{
    private Directory $package;

    public function __construct(Directory $package)
    {
        $this->package = $package;
    }

    public function value(): string
    {
        $gitConfigFile = $this->package->file('.git/config');
        if (!$gitConfigFile->exists()) { return ''; }

        $config = parse_ini_string($gitConfigFile->contents(), true);
        $uri    = $config['remote upstream']['url'] ?? $config['remote origin']['url'] ?? '';
        if (!$uri) { return ''; }

        $uriPath = str_replace(':', '/', $uri);
        return basename(dirname($uriPath)) . '/' . basename($uriPath, '.git');
    }
}
