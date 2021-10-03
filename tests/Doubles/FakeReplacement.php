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

use Shudd3r\PackageFiles\Replacement;
use Shudd3r\PackageFiles\Application\RuntimeEnv;


class FakeReplacement extends Replacement
{
    protected ?string $optionName  = 'option';
    protected ?string $inputPrompt = 'Provide value';

    private ?string $value;
    private bool    $isValid;

    public function __construct(
        RuntimeEnv $env,
        ?string $fallback = null,
        ?string $value = 'default value',
        bool $isValid = true
    ) {
        $this->value   = $value;
        $this->isValid = $isValid;
        parent::__construct($env, $fallback);
    }

    protected function isValid(string $value): bool
    {
        return $this->isValid;
    }

    protected function defaultValue(array $options): string
    {
        return $this->value ?? $this->fallbackValue($options);
    }
}
