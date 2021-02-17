<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests;

use Shudd3r\PackageFiles\Application\Token\InitialContents;
use Shudd3r\PackageFiles\Application\Token\OriginalContents;
use Shudd3r\PackageFiles\Application\Token\Replacement;


class TestEnvSetup
{
    public const PACKAGE_NAME  = 'package.name';
    public const PACKAGE_DESC  = 'description.text';
    public const SRC_NAMESPACE = 'namespace.src';
    public const REPO_NAME     = 'repository.name';

    public const SKELETON_FILE = 'file/generate.txt';

    public Doubles\FakeRuntimeEnv $env;

    public function __construct(Doubles\FakeRuntimeEnv $env = null)
    {
        $this->env = $env ?? new Doubles\FakeRuntimeEnv();

        $this->env->package()->path  = '/path/to/package/directory';
        $this->env->skeleton()->path = '/path/to/skeleton/files';

        $this->env->skeleton()->addFile(self::SKELETON_FILE, $this->defaultTemplate());

        $replacements = $this->env->replacements();

        $replacements->add(self::PACKAGE_NAME, fn($env) => new Replacement\PackageName($env));

        $packageName = $replacements->replacement(self::PACKAGE_NAME);
        $replacements->add(self::REPO_NAME, fn($env) => new Replacement\RepositoryName($env, $packageName));
        $replacements->add(self::PACKAGE_DESC, fn($env) => new Replacement\PackageDescription($env, $packageName));
        $replacements->add(self::SRC_NAMESPACE, fn($env) => new Replacement\SrcNamespace($env, $packageName));
    }

    public function addMetaData(array $data = []): void
    {
        $this->env->metaDataFile()->write(json_encode($this->data($data), JSON_PRETTY_PRINT));
    }

    public function addComposer(array $data = []): void
    {
        $this->env->package()->addFile('composer.json', $this->composer($data));
    }

    public function addGeneratedFile(array $override = []): void
    {
        $data = $this->data($override);
        $this->env->package()->addFile(self::SKELETON_FILE, $this->render($data));
    }

    public function render(array $replacements = [], bool $orig = true, string $template = null): string
    {
        $template ??= $this->defaultTemplate(true, $orig);

        $template = $orig ? $this->replaceOriginalContent($template) : $this->removeOriginalContent($template);

        foreach ($replacements as $name => $replacement) {
            $template = str_replace('{' . $name . '}', $replacement, $template);
        }

        return $template;
    }

    public function defaultTemplate(bool $render = false, bool $orig = true): string
    {
        $descToken = $this->placeholder(self::PACKAGE_DESC);
        $repoToken = $this->placeholder(self::REPO_NAME);
        $packToken = $this->placeholder(self::PACKAGE_NAME);
        $nameToken = $this->placeholder(self::SRC_NAMESPACE);
        $origToken = $this->placeholder(OriginalContents::PLACEHOLDER);

        $marker    = '...Your own contents here...';
        $initToken = $render
            ? ($orig ? $origToken : $marker)
            : InitialContents::CONTENT_START . $marker . InitialContents::CONTENT_END;

        return <<<TPL
            This is a template for {$repoToken} in a {$packToken} package{$origToken}, which
            is "{$descToken}" with `src` directory files in `{$nameToken}` namespace.
            
            {$initToken}
            
            THE END.
            TPL;
    }

    public function composer(array $override = []): string
    {
        $data = $this->data(array_filter($override));

        $ns = str_replace('//', '////', $data['namespace.src']) . '\\';
        $composer = [
            'name'              => $data['package.name'],
            'description'       => $data['description.text'],
            'type'              => 'library',
            'license'           => 'MIT',
            'authors'           => [['name' => 'Shudd3r', 'email' => 'q3.shudder@gmail.com']],
            'autoload'          => ['psr-4' => [$ns => 'src/']],
            'autoload-dev'      => ['psr-4' => [$ns . 'Tests\\' => 'tests/']],
            'minimum-stability' => 'stable'
        ];

        $removeMap = [
            'package.name'     => 'name',
            'description.text' => 'description',
            'namespace.src'    => 'autoload'
        ];

        foreach ($removeMap as $remove => $key) {
            if (array_key_exists($remove, $override) && is_null($override[$remove])) {
                unset($composer[$key]);
            }
        }

        return json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    }

    public function data(array $override = []): array
    {
        $data = [
            'package.name'     => 'default/package',
            'repository.name'  => 'default/repo',
            'description.text' => 'This is default description',
            'namespace.src'    => 'Default\\Namespace'
        ];

        return array_merge($data, $override);
    }

    private function replaceOriginalContent(string $template): string
    {
        $contents = [
            ' (and this is some original content not present in template file)',
            '--- this was extracted from package file ---'
        ];

        $parts = explode(OriginalContents::PLACEHOLDER, $template);

        $template = array_shift($parts);
        foreach ($contents as $replace) {
            $template .= $replace . array_shift($parts);
        }

        return $template;
    }

    private function removeOriginalContent(string $template): string
    {
        return str_replace($this->placeholder(OriginalContents::PLACEHOLDER), '', $template);
    }

    private function placeholder(string $name)
    {
        return '{' . $name . '}';
    }
}
