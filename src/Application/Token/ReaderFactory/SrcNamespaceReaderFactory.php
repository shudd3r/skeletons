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

use Shudd3r\PackageFiles\Application\Token\ValueToken;
use Shudd3r\PackageFiles\Application\Token\CompositeValueToken;
use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Application\RuntimeEnv;


class SrcNamespaceReaderFactory extends ValueReaderFactory
{
    protected ?string $inputPrompt = 'Source files namespace';
    protected ?string $optionName  = 'ns';

    private PackageNameReaderFactory $packageName;

    public function __construct(RuntimeEnv $env, array $options, PackageNameReaderFactory $packageName)
    {
        $this->packageName = $packageName;
        parent::__construct($env, $options);
    }

    public function token(string $name, string $value): ?ValueToken
    {
        foreach (explode('\\', $value) as $label) {
            $isValidLabel = (bool) preg_match('#^[a-z_\x7f-\xff][a-z0-9_\x7f-\xff]*$#Di', $label);
            if (!$isValidLabel) { return null; }
        }

        $subToken = new ValueToken($name . '.esc', str_replace('\\', '\\\\', $value));
        return new CompositeValueToken($name, $value, $subToken);
    }

    protected function defaultSource(): Source
    {
        $callback = fn() => $this->namespaceFromComposer() ?? $this->namespaceFromPackageName();
        return $this->userSource(new Source\CallbackSource($callback));
    }

    private function namespaceFromComposer(): ?string
    {
        if (!$psr = $this->env->composer()->array('autoload.psr-4')) { return null; }
        $namespace = array_search('src/', $psr, true);

        return $namespace ? rtrim($namespace, '\\') : null;
    }

    private function namespaceFromPackageName(): string
    {
        [$vendor, $package] = explode('/', $this->packageName->initialToken('')->value());
        return $this->toPascalCase($vendor) . '\\' . $this->toPascalCase($package);
    }

    private function toPascalCase(string $name): string
    {
        $name = ltrim($name, '0..9');
        return implode('', array_map(fn ($part) => ucfirst($part), preg_split('#[_.-]#', $name)));
    }
}
