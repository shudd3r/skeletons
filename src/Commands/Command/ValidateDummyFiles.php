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
use Shudd3r\Skeletons\Templates\DummyFiles;
use Shudd3r\Skeletons\Environment\Output;
use Shudd3r\Skeletons\Environment\Files\File;


class ValidateDummyFiles implements Command
{
    private Directory  $package;
    private DummyFiles $dummies;
    private Output     $output;

    public function __construct(Directory $package, DummyFiles $dummies, Output $output)
    {
        $this->package = $package;
        $this->dummies = $dummies;
        $this->output  = $output;
    }

    public function execute(): void
    {
        $fileIndex = $this->dummies->verifiedFiles($this->package);
        $this->showMissingFiles($fileIndex->missingFiles());
        $this->showRedundantFiles($fileIndex->redundantFiles());
    }

    private function showMissingFiles(array $files): void
    {
        if (!$files) { return; }
        $this->output->send('- Missing dummy files for required directories:' . PHP_EOL);
        $this->displayFilenames($files);
        $errorMessage = <<<ERROR
          Directories that contain these files are required by skeleton,
          and dummy files are necessary to make empty directories deployable.
          You can create them automatically with `sync` command.
        
        ERROR;
        $this->output->send($errorMessage, 1);
    }

    private function showRedundantFiles(array $files): void
    {
        if (!$files) { return; }
        $this->output->send('- Redundant dummy files found:' . PHP_EOL);
        $this->displayFilenames($files);
        $errorMessage = <<<ERROR
          These dummy files are no longer needed.
          You can remove them automatically with `sync` command.
        
        ERROR;
        $this->output->send($errorMessage, 1);
    }

    private function displayFilenames(array $files): void
    {
        $displayFilename = fn (File $file) => $this->output->send('    ' . $file->name() . PHP_EOL);
        array_walk($files, $displayFilename);
    }
}
