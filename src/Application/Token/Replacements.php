<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token;

use Shudd3r\PackageFiles\ReplacementReader;
use Shudd3r\PackageFiles\Application\Exception;


class Replacements
{
    /** @var ReplacementReader[] */
    private array $replacements;
    private array $currentRefs;

    public function __construct(array $replacements = [])
    {
        $this->replacements = $replacements;
    }

    public function add(string $name, ReplacementReader $replacement): void
    {
        if (isset($this->replacements[$name])) {
            throw new Exception\ReplacementOverwriteException();
        }

        $this->replacements[$name] = $replacement;
    }

    public function valueOf(string $replacementName, array $options): string
    {
        $isCurrentRef = $this->currentRefs[$replacementName] ?? false;
        if ($isCurrentRef) { return ''; }

        $replacement = $this->replacements[$replacementName] ?? null;
        if (!$replacement) { return ''; }

        $this->currentRefs[$replacementName] = true;
        $token = $replacement->initialToken($replacementName, $options, $this);
        $this->currentRefs[$replacementName] = false;

        return $token ? $token->value() : '';
    }

    public function init(array $options): Reader
    {
        return new Reader\InitialReader($this->replacements, $options, $this);
    }

    public function validate(): Reader
    {
        return new Reader\ValidationReader($this->replacements);
    }

    public function update(array $options): Reader
    {
        return new Reader\UpdateReader($this->replacements, $options);
    }
}
