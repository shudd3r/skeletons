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
    private Files     $dummies;
    private ?Output   $output;
    private bool      $validate;

    public function __construct(Directory $package, Files $dummies, Output $output = null, bool $validate = false)
    {
        $this->package  = $package;
        $this->dummies  = $dummies;
        $this->output   = $output;
        $this->validate = $validate;
    }

    public function execute(): void
    {
        $redundantFiles = $this->redundantDummyFiles();
        if (!$redundantFiles) { return; }

        if ($this->output) {
            $action = $this->validate ? 'Found' : 'Removing';
            $this->output->send($action . ' redundant dummy files:' . PHP_EOL);
        }

        array_walk($redundantFiles, fn ($filename) => $this->handleRedundantFile($filename));
        if ($this->validate) { $this->sendErrorMessage(); }
    }

    private function redundantDummyFiles(): array
    {
        $index    = [];
        $multiple = [];
        foreach ($this->dummies->fileList() as $file) {
            $filename = $file->name();
            if (!$this->package->file($filename)->exists()) { continue; }
            if (!strpos($filename, '/')) { continue; }

            $systemPath = $this->normalized($filename, DIRECTORY_SEPARATOR);
            $dirname    = dirname($systemPath);
            $count      = count($this->package->subdirectory($dirname)->fileList());

            if ($count === 1) { continue; }
            if (!isset($index[$dirname])) {
                $index[$dirname] = $filename;
                continue;
            }

            $multiple[$dirname] ??= [$index[$dirname]];
            $multiple[$dirname][] = $filename;
            if (count($multiple[$dirname]) === $count) { unset($index[$dirname], $multiple[$dirname]); }
        }

        return $this->mergeMultipleFiles(array_values($index), $multiple);
    }

    private function handleRedundantFile(string $filename): void
    {
        $this->output and $this->output->send('   x ' . $filename . PHP_EOL);
        $this->validate or $this->package->file($filename)->remove();
    }

    private function sendErrorMessage(): void
    {
        $errorMessage = <<<ERROR
            These dummy files are no longer needed.
            You can remove them automatically with `sync` command.
        
        ERROR;
        $this->output->send($errorMessage, 1);
    }

    private function mergeMultipleFiles(array $filenames, array $multiple): array
    {
        foreach ($multiple as $files) {
            foreach ($files as $filename) {
                $filenames[] = $filename;
            }
        }
        return $filenames;
    }
}
