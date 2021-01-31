<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token\ReaderFactory;

use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Application\RuntimeEnv;


class PackageDescriptionReaderFactory extends ValueReaderFactory
{
    protected ?string $inputPrompt = 'Package description';
    protected ?string $optionName  = 'desc';

    private PackageNameReaderFactory $packageName;

    public function __construct(RuntimeEnv $env, array $options, PackageNameReaderFactory $packageName)
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
        $packageName = $this->packageName->initialToken('');
        $callback    = fn() => $this->env->composer()->value('description') ?? $packageName->value() . ' package';
        return $this->userSource(new Source\CallbackSource($callback));
    }
}
