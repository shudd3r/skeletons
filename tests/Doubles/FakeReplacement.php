<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Doubles;

use Shudd3r\PackageFiles\Application\Token\Replacement;
use Shudd3r\PackageFiles\Application\Token\Source;


class FakeReplacement extends Replacement
{
    protected ?string $optionName  = 'option';
    protected ?string $inputPrompt = 'Provide value';

    private bool $isValid;

    public function __construct(FakeRuntimeEnv $env, bool $isValid = true)
    {
        $this->isValid = $isValid;
        parent::__construct($env);
    }

    protected function isValid(string $value): bool
    {
        return $this->isValid;
    }

    protected function defaultSource(array $options): Source
    {
        return $this->userSource(new Source\PredefinedValue('default value'), $options);
    }
}
