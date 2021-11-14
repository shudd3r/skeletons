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


class HandleDummyFiles implements Command
{
    use Files\Paths;

    private Directory $package;
    private Files     $files;
    private ?Output   $output;
    private bool      $validate;

    public function __construct(Directory $package, Files $files, Output $output = null, bool $validate = false)
    {
        $this->package  = $package;
        $this->files    = $files;
        $this->output   = $output;
        $this->validate = $validate;
    }

    public function execute(): void
    {
        $invalid = false;
        foreach ($this->fileIndex() as $subdirectory => $files) {
            if ($this->essentialDummies($subdirectory, $files)) { continue; }

            if (!$invalid && $this->output) {
                $action = $this->validate ? 'Found' : 'Removing';
                $this->output->send($action . ' redundant dummy files:' . PHP_EOL);
            }

            $invalid = true;
            $handler = function (Files\File $file): void {
                $this->output and $this->output->send('   x ' . $file->name() . PHP_EOL);
                $this->validate or $file->remove();
            };
            array_walk($files, $handler);
        }

        if ($invalid && $this->validate) { $this->sendErrorMessage(); }
    }

    private function fileIndex(): array
    {
        $fileIndex = [];
        foreach ($this->files->fileList() as $file) {
            $filename = $file->name();
            if (!$this->package->file($filename)->exists()) { continue; }
            if (!strpos($filename, '/')) { continue; }

            $systemPath = $this->normalized($filename, DIRECTORY_SEPARATOR);
            $dirname    = dirname($systemPath);
            $filename   = basename($systemPath);

            $fileIndex[$dirname][$filename] = $file;
        }

        return $fileIndex;
    }

    private function essentialDummies(string $subdirectory, array $dummyFiles): bool
    {
        $packageFiles = $this->package->subdirectory($subdirectory)->fileList();
        foreach ($packageFiles as $idx => $file) {
            $filename = $file->name();
            if (!isset($dummyFiles[$filename])) { continue; }
            unset($packageFiles[$idx]);
        }

        return empty($packageFiles);
    }

    private function sendErrorMessage(): void
    {
        $errorMessage = <<<ERROR
            These dummy files are no longer needed.
            You can remove them automatically with `sync` command.
        
        ERROR;
        $this->output->send($errorMessage, 1);
    }
}
