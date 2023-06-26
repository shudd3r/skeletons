<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Commands\Command;

use Shudd3r\Skeletons\Commands\Command;
use Shudd3r\Skeletons\Environment\Files\Directory;
use Shudd3r\Skeletons\Environment\Files;
use Shudd3r\Skeletons\Environment\Output;


class ValidateDummyFiles implements Command
{
    use Files\Paths;

    private Directory $package;
    private Files     $dummies;
    private Output    $output;

    public function __construct(Directory $package, Files $dummies, Output $output)
    {
        $this->package = $package;
        $this->dummies = $dummies;
        $this->output  = $output;
    }

    public function execute(): void
    {
        $fileIndex = $this->fileIndex();
        $this->showMissingFiles($fileIndex['missing']);
        $this->showRedundantFiles($fileIndex['redundant']);
    }

    private function fileIndex(): array
    {
        $index = ['redundant' => [], 'missing' => []];
        foreach ($this->dummies->fileList() as $file) {
            $filename = $file->name();
            if (!strpos($filename, '/')) { continue; }

            $systemPath = $this->normalized($filename, DIRECTORY_SEPARATOR);
            $dirname    = dirname($systemPath);
            $count      = count($this->package->subdirectory($dirname)->fileList());

            if ($count === 0) {
                $index['missing'][] = $filename;
                continue;
            }
            if ($count === 1 || !$this->package->file($filename)->exists()) { continue; }
            $index['redundant'][] = $filename;
        }

        return $index;
    }

    private function showRedundantFiles(array $redundantFiles): void
    {
        if (!$redundantFiles) { return; }
        $this->output->send('- Redundant dummy files found:' . PHP_EOL);
        $this->displayFilenames($redundantFiles);
        $errorMessage = <<<ERROR
          These dummy files are no longer needed.
          You can remove them automatically with `sync` command.
        
        ERROR;
        $this->output->send($errorMessage, 1);
    }

    private function showMissingFiles(array $missingFiles): void
    {
        if (!$missingFiles) { return; }
        $this->output->send('- Missing dummy files for required directories:' . PHP_EOL);
        $this->displayFilenames($missingFiles);
        $errorMessage = <<<ERROR
          Directories that contain these files are required by skeleton,
          and dummy files are necessary to make empty directories deployable.
          You can create them automatically with `sync` command.
        
        ERROR;
        $this->output->send($errorMessage, 1);
    }

    private function displayFilenames(array $filenames): void
    {
        foreach ($filenames as $filename) {
            $this->output->send('    ' . $filename . PHP_EOL);
        }
    }
}
