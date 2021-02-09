<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token\Replacement;

use Shudd3r\PackageFiles\Application\Token\Replacement;
use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Application\RuntimeEnv;


class PackageDescription extends Replacement
{
    protected ?string $inputPrompt = 'Package description';
    protected ?string $optionName  = 'desc';

    private PackageName $packageName;

    public function __construct(RuntimeEnv $env, array $options, PackageName $packageName)
    {
        $this->packageName = $packageName;
        parent::__construct($env, $options);
    }

    protected function isValid(string $value): bool
    {
        return !empty($value);
    }

    protected function defaultSource(): Source
    {
        $callback = fn() => $this->env->composer()->value('description') ?? $this->fromPackageName();
        return $this->userSource(new Source\CallbackSource($callback));
    }

    private function fromPackageName(): string
    {
        $package = $this->packageName->sourceValue();
        return $package ? $package . ' package' : '';
    }
}
