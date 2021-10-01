<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Replacement;

use Shudd3r\PackageFiles\Replacement;
use Shudd3r\PackageFiles\Application\Token\ValueToken;
use Shudd3r\PackageFiles\Application\Token\CompositeValueToken;
use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Application\RuntimeEnv;


class SrcNamespace extends Replacement
{
    protected ?string $inputPrompt = 'Source files namespace';
    protected ?string $optionName  = 'ns';

    private ?string $packageNamePlaceholder;

    public function __construct(RuntimeEnv $env, ?string $packageNamePlaceholder = null)
    {
        $this->packageNamePlaceholder = $packageNamePlaceholder;
        parent::__construct($env);
    }

    protected function token(string $name, string $value): ?ValueToken
    {
        if (!$this->isValid($value)) { return null; }

        $subToken = new ValueToken($name . '.esc', str_replace('\\', '\\\\', $value));
        return new CompositeValueToken($name, $value, $subToken);
    }

    protected function isValid(string $value): bool
    {
        foreach (explode('\\', $value) as $label) {
            $isValidLabel = (bool) preg_match('#^[a-z_\x7f-\xff][a-z0-9_\x7f-\xff]*$#Di', $label);
            if (!$isValidLabel) { return false; }
        }

        return true;
    }

    protected function defaultSource(array $options): Source
    {
        $callback = fn() => $this->namespaceFromComposer() ?? $this->namespaceFromPackageName($options);
        return $this->userSource(new Source\CallbackSource($callback), $options);
    }

    private function namespaceFromComposer(): ?string
    {
        if (!$psr = $this->env->composer()->array('autoload.psr-4')) { return null; }
        $namespace = array_search('src/', $psr, true);

        return $namespace ? rtrim($namespace, '\\') : null;
    }

    private function namespaceFromPackageName(array $options): string
    {
        if (!$this->packageNamePlaceholder) { return ''; }

        $replacement = $this->env->replacements()->replacement($this->packageNamePlaceholder);
        if (!$replacement) { return ''; }

        $token = $replacement->initialToken($this->packageNamePlaceholder, $options);
        if (!$token) { return ''; }

        [$vendor, $package] = explode('/', $token->value());
        $namespace = $this->toPascalCase($vendor) . '\\' . $this->toPascalCase($package);

        return $this->isValid($namespace) ? $namespace : '';
    }

    private function toPascalCase(string $name): string
    {
        $name = ltrim($name, '0..9');
        return implode('', array_map(fn ($part) => ucfirst($part), preg_split('#[_.-]#', $name)));
    }
}
