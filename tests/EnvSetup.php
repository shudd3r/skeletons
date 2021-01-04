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

use Shudd3r\PackageFiles\Application\Token\Reader;


class EnvSetup
{
    public const SKELETON_FILE = 'file/generate.txt';

    public Doubles\FakeRuntimeEnv $env;

    public function __construct(Doubles\FakeRuntimeEnv $env = null)
    {
        $this->env = $env ?? new Doubles\FakeRuntimeEnv();

        $this->env->package()->path  = '/path/to/package/directory';
        $this->env->skeleton()->path = '/path/to/skeleton/files';

        $this->env->skeleton()->addFile(self::SKELETON_FILE, $this->defaultTemplate());
    }

    public function addMetaData(array $data = []): void
    {
        $metaData = $this->metaData($data);
        $this->env->metaDataFile()->write(json_encode($metaData, JSON_PRETTY_PRINT));
    }

    public function addTemplate(string $filename, string $template = null): void
    {
        $this->env->skeleton()->addFile($filename, $template ?? $this->defaultTemplate());
    }

    public function addComposer(array $data = []): void
    {
        $this->env->package()->addFile('composer.json', $this->composer($data));
    }

    public function addPackageFile(string $filename, string $contents = ''): void
    {
        $this->env->package()->addFile($filename, $contents);
    }

    public function addSkeletonFile(string $filename, string $contents = ''): void
    {
        $this->env->skeleton()->addFile($filename, $contents);
    }

    public function render(array $replacements = [], string $template = null, ?array $orig = null): string
    {
        $template ??= $this->defaultTemplate();

        $template = $this->replaceOriginalContent($template, $orig ?? [
            ' (and this is some original content not present in template file)',
            '--- this was extracted from package file ---'
        ]);

        foreach ($replacements as $name => $replacement) {
            $template = str_replace('{' . $name . '}', $replacement, $template);
        }

        return $template;
    }

    public function defaultTemplate(): string
    {
        return <<<TPL
            This is a template for {repository.name} in a {package.name} package{original.content}, which
            is "{description.text}" with `src` directory files in `{namespace.src}` namespace.
            
            {original.content}
            TPL;
    }

    public function metaData(array $override = []): array
    {
        $data = $this->data($override);

        return [
            Reader\PackageName::class        => $data['package.name'],
            Reader\RepositoryName::class     => $data['repository.name'],
            Reader\PackageDescription::class => $data['description.text'],
            Reader\SrcNamespace::class       => $data['namespace.src']
        ];
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

        return $override + $data;
    }

    private function replaceOriginalContent(string $template, array $contents = []): string
    {
        if (!$contents) { return str_replace('{original.content}', '', $template); }

        $parts = explode('{original.content}', $template);

        $template = array_shift($parts);
        foreach ($contents as $replace) {
            $template .= $replace . array_shift($parts);
        }

        return $template;
    }
}
