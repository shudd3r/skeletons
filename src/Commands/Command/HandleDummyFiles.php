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


class HandleDummyFiles implements Command
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
        $this->addFiles($fileIndex->missingFiles());
        $this->removeFiles($fileIndex->redundantFiles());
    }

    private function addFiles(array $files): void
    {
        if (!$files) { return; }
        $this->output->send('- Creating dummy files for required directories:' . PHP_EOL);
        $addFile = fn (File $file) => $this->package->file($this->displayFilename($file))->write($file->contents());
        array_walk($files, $addFile);
    }

    private function removeFiles(array $files): void
    {
        if (!$files) { return; }
        $this->output->send('- Removing redundant dummy files:' . PHP_EOL);
        $removeFile = fn (File $file) => $this->package->file($this->displayFilename($file))->remove();
        array_walk($files, $removeFile);
    }

    private function displayFilename(File $file): string
    {
        $filename = $file->name();
        $this->output->send('    ' . $filename . PHP_EOL);
        return $filename;
    }
}
