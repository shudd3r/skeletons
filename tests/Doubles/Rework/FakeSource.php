<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Doubles\Rework;

use Shudd3r\Skeletons\Rework\Replacements\Source;
use Shudd3r\Skeletons\Replacements\Data\ComposerJsonData;
use Shudd3r\Skeletons\Environment\Files\File\VirtualFile;
use Closure;


class FakeSource implements Source
{
    private array  $commandArgs;
    private array  $metaData;
    private array  $inputStrings = [];
    private array  $composerData = [];
    private array  $tokenValues  = [];
    private array  $fileContents = [];
    private string $packagePath  = '/package';

    private string $promptUsed = '';

    public function __construct(array $metaData = [], array $commandArgs = [])
    {
        $this->commandArgs = $commandArgs;
        $this->metaData    = $metaData;
    }

    public static function create(array $metaData = [], array $commandArgs = []): self
    {
        return new self($metaData, $commandArgs);
    }

    public function commandArgument(string $argumentName): string
    {
        return $this->commandArgs[$argumentName] ?? '';
    }

    public function inputString(string $prompt, Closure $isValid): string
    {
        $this->promptUsed = $prompt;
        return array_shift($this->inputStrings) ?: '';
    }

    public function metaValueOf(string $name): ?string
    {
        return $this->metaData[$name] ?? null;
    }

    public function composer(): ComposerJsonData
    {
        $file = new VirtualFile('composer.json', $this->composerData ? json_encode($this->composerData) : '{}');
        return new ComposerJsonData($file);
    }

    public function fileContents(string $filename): string
    {
        return $this->fileContents[$filename] ?? '';
    }

    public function packagePath(): string
    {
        return $this->packagePath;
    }

    public function tokenValueOf(string $name): string
    {
        return $this->tokenValues[$name] ?? '';
    }

    public function promptUsed(): string
    {
        return $this->promptUsed;
    }

    public function withComposerData(array $data): self
    {
        $this->composerData = $data;
        return $this;
    }

    public function withInputStrings(string ...$inputs): self
    {
        $this->inputStrings = $inputs;
        return $this;
    }

    public function withFileContents(string $filename, string $contents): self
    {
        $this->fileContents[$filename] = $contents;
        return $this;
    }

    public function withPackagePath(string $path): self
    {
        $this->packagePath = $path;
        return $this;
    }

    public function withFallbackTokenValue(string $tokenName, string $value): self
    {
        $this->tokenValues[$tokenName] = $value;
        return $this;
    }
}
