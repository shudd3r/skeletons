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
        $fileIndex = $this->fileIndex();
        $this->handleMissingFiles($fileIndex['missing']);
        $this->handleRedundantFiles($fileIndex['redundant']);
    }

    private function fileIndex(): array
    {
        $index = ['redundant' => [], 'missing' => []];
        $multiple = [];
        foreach ($this->dummies->fileList() as $file) {
            $filename = $file->name();
            if (!strpos($filename, '/')) { continue; }

            $systemPath = $this->normalized($filename, DIRECTORY_SEPARATOR);
            $dirname    = dirname($systemPath);
            $count      = count($this->package->subdirectory($dirname)->fileList());

            if ($count === 1) { continue; }
            if ($count === 0) {
                $index['missing'][] = $filename;
                continue;
            }

            if (!isset($index['redundant'][$dirname])) {
                $index['redundant'][$dirname] = $filename;
                continue;
            }

            $multiple[$dirname] ??= [$index['redundant'][$dirname]];
            $multiple[$dirname][] = $filename;
            if (count($multiple[$dirname]) === $count) { unset($index['redundant'][$dirname], $multiple[$dirname]); }
        }

        return [
            'redundant' => $this->mergeMultipleFiles(array_values($index['redundant']), $multiple),
            'missing'   => $index['missing']
        ];
    }

    private function handleRedundantFiles(array $redundantFiles): void
    {
        if (!$redundantFiles) { return; }
        if ($this->output) {
            $action = $this->validate ? 'Found' : 'Removing';
            $this->output->send('- ' . $action . ' redundant dummy files:' . PHP_EOL);
        }

        foreach ($redundantFiles as $filename) {
            $this->displayFilename($filename);
            if ($this->validate) { continue; }
            $this->package->file($filename)->remove();
        }

        $this->sendRedundantErrorMessage();
    }

    private function handleMissingFiles(array $missingFiles): void
    {
        if (!$missingFiles) { return; }
        if ($this->output) {
            $action = $this->validate ? 'Discovered' : 'Creating';
            $this->output->send('- ' . $action . ' missing dummy files:' . PHP_EOL);
        }

        foreach ($missingFiles as $filename) {
            $this->displayFilename($filename);
            if ($this->validate) { continue; }
            $this->package->file($filename)->write($this->dummies->file($filename)->contents());
        }

        $this->sendMissingErrorMessage();
    }

    private function displayFilename(string $filename): void
    {
        if (!$this->output) { return; }
        $this->output->send('    ' . $filename . PHP_EOL);
    }

    private function sendRedundantErrorMessage(): void
    {
        if (!$this->validate) { return; }
        $errorMessage = <<<ERROR
          These dummy files are no longer needed.
          You can remove them automatically with `sync` command.
        
        ERROR;
        $this->output->send($errorMessage, 1);
    }

    private function sendMissingErrorMessage(): void
    {
        if (!$this->validate) { return; }
        $errorMessage = <<<ERROR
          Directories that contain these files are required by skeleton,
          and dummy files are necessary to make empty directories deployable.
          You can create them automatically with `sync` command.
        
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
