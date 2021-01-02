<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Processor;

use Shudd3r\PackageFiles\Application\Processor;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Application\Token;


class SkeletonFilesProcessor implements Processor
{
    private Directory $skeleton;
    private Factory   $factory;

    public function __construct(Directory $skeleton, Factory $factory)
    {
        $this->factory  = $factory;
        $this->skeleton = $skeleton;
    }

    public function process(Token $token): bool
    {
        $status = true;
        foreach ($this->skeleton->files() as $file) {
            $status = $this->factory->processor($file)->process($token) && $status;
        }

        return $status;
    }
}
