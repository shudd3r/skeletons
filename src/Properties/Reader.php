<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Properties;

use Shudd3r\PackageFiles\Properties;
use Shudd3r\PackageFiles\Application\Output;
use Exception;


class Reader
{
    private Source $source;
    private Output $output;

    public function __construct(Source $source, Output $output)
    {
        $this->source = $source;
        $this->output = $output;
    }

    public function properties(): Properties
    {
        $repository = $this->create(fn() => new Repository($this->source->repositoryName()));
        $package    = $this->create(fn() => new Package($this->source->packageName(), $this->source->packageDescription()));
        $namespace  = $this->create(fn() => new MainNamespace($this->source->sourceNamespace()));

        if (!$repository || !$package || !$namespace) {
            throw new Exception('Cannot process unresolved properties');
        }

        return new Properties($repository, $package, $namespace);
    }

    public function create(callable $callback)
    {
        try {
            return $callback();
        } catch (Exception $e) {
            $this->output->send($e->getMessage(), 1);
            return null;
        }
    }
}
