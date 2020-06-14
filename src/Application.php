<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles;

use Shudd3r\PackageFiles\Command\Routing;
use Shudd3r\PackageFiles\Application\Output;
use Exception;


class Application
{
    private Output  $output;
    private Routing $routing;

    public function __construct(Output $output, Routing $routing)
    {
        $this->output  = $output;
        $this->routing = $routing;
    }

    /**
     * Builds package environment files.
     *
     * $options array is config name keys ('package', 'repo', 'desc' & 'ns')
     * with corresponding values and 'interactive' key (or short version: 'i')
     * with any not null value (also false) that when given activates CLI input
     * of not provided options (omitted or assigned to null) otherwise these
     * values will try to be resolved automatically.
     *
     * @example Array with all values defined for this package: [
     *     'package' => 'polymorphine/dev',
     *     'repo'    => 'polymorphine/dev',
     *     'desc'    => 'Development tools & coding standard scripts for Polymorphine libraries',
     *     'ns'      => 'Polymorphine\Dev'
     * ]
     *
     * @param string $command
     * @param array  $options
     *
     * @return int Exit code
     */
    public function run(string $command, array $options = []): int
    {
        try {
            $this->routing->factory($command)
                          ->command()
                          ->execute($options);
        } catch (Exception $e) {
            $this->output->send($e->getMessage(), 1);
        }

        return $this->output->exitCode();
    }
}
