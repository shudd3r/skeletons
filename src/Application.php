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

use Shudd3r\PackageFiles\Application\Routing;
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
     * @param string $command Command name (usually first CLI argument)
     * @param array  $options Command options
     *
     * @return int Exit code where 0 means execution without errors
     */
    public function run(string $command, array $options = []): int
    {
        try {
            $this->routing->command($command)->execute($options);
        } catch (Exception $e) {
            $this->output->send($e->getMessage(), 1);
        }

        return $this->output->exitCode();
    }
}
