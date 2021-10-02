<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles;

use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Application\Token\ValueToken;
use Shudd3r\PackageFiles\Application\Token\Source;


abstract class Replacement
{
    private ?string $fallback;

    protected RuntimeEnv $env;

    protected ?string $inputPrompt;
    protected ?string $optionName;

    private ?ValueToken $initialToken;

    public function __construct(RuntimeEnv $env, ?string $fallbackPlaceholder = null)
    {
        $this->env      = $env;
        $this->fallback = $fallbackPlaceholder;
    }

    public function initialToken(string $name, array $options): ?ValueToken
    {
        return $this->initialToken ??= $this->token($name, $this->defaultSource($options)->value());
    }

    public function validationToken(string $name): ?ValueToken
    {
        $value = $this->metaDataSource($name)->value();
        return $this->token($name, $value);
    }

    public function updateToken(string $name, array $options): ?ValueToken
    {
        $value = $this->userSource($this->metaDataSource($name), $options)->value();
        return $this->token($name, $value);
    }

    protected function token(string $name, string $value): ?ValueToken
    {
        return $this->isValid($value) ? new ValueToken($name, $value) : null;
    }

    abstract protected function isValid(string $value): bool;

    abstract protected function defaultSource(array $options): Source;

    protected function metaDataSource(string $namespace): Source
    {
        $callback = fn() => $this->env->metaData()->value($namespace) ?? '';
        return new Source\CallbackSource($callback);
    }

    protected function userSource(Source $source, array $options): Source
    {
        return $this->interactive($this->option($source, $options), $options);
    }

    protected function fallbackValue(array $options): string
    {
        return $this->fallback ? $this->env->replacements()->valueOf($this->fallback, $options) : '';
    }

    private function option(Source $default, array $options): Source
    {
        $hasOption = isset($this->optionName) && isset($options[$this->optionName]);
        return $hasOption
            ? new Source\PredefinedValue($options[$this->optionName])
            : $default;
    }

    private function interactive(Source $source, array $options): Source
    {
        $fromInput = isset($this->inputPrompt) && (isset($options['i']) || isset($options['interactive']));
        return $fromInput
            ? new Source\InteractiveInput($this->inputPrompt, $this->env->input(), $source)
            : $source;
    }
}
