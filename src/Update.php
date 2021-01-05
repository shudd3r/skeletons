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

use Shudd3r\PackageFiles\Application\Command;
use Shudd3r\PackageFiles\Environment\Command as CommandInterface;
use Shudd3r\PackageFiles\Application\Token\TokenCache;
use Shudd3r\PackageFiles\Application\Token\Reader;
use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Application\Processor;
use Shudd3r\PackageFiles\Application\Template;


class Update extends Command\Factory
{
    public function command(): CommandInterface
    {
        $validation = new Validate($this->env, $this->options);
        $cache      = new TokenCache();

        $tokenReader   = new Reader\CompositeTokenReader(...$this->tokenReaders());
        $processTokens = new Command\TokenProcessor($tokenReader, $this->processor($cache), $this->env->output());
        $writeMetaData = new Command\WriteMetaData($tokenReader, $this->env->metaDataFile());

        $metaDataExists    = new Command\Precondition\CheckFileExists($this->env->metaDataFile(), true);
        $synchronizedFiles = $validation->synchronizedSkeleton($cache);

        return new Command\ProtectedCommand(
            new Command\CommandSequence($processTokens, $writeMetaData),
            new Command\Precondition\Preconditions($metaDataExists, $synchronizedFiles)
        );
    }

    protected function tokenReaders(): array
    {
        $default = new Source\MetaDataFile($this->env->metaDataFile(), new Source\PredefinedValue(''));

        $source  = $this->interactive('Packagist package name', $this->option('package', $default));
        $package = new Reader\PackageName($source);

        $source = $this->interactive('Github repository name', $this->option('repo', $default));
        $repo   = new Reader\RepositoryName($source);

        $source = $this->interactive('Github repository name', $this->option('desc', $default));
        $desc   = new Reader\PackageDescription($source);

        $source    = $this->interactive('Source files namespace', $this->option('ns', $default));
        $namespace = new Reader\SrcNamespace($source);

        return [$package, $repo, $desc, $namespace];
    }

    protected function processor(TokenCache $cache): Processor
    {
        $composerFile     = $this->env->package()->file('composer.json');
        $template         = new Template\ComposerJsonTemplate($composerFile);
        $generateComposer = new Processor\GenerateFile($template, $composerFile);

        $generatorFactory = new Processor\FileProcessors\UpdatedFileGenerators($this->env->package(), $cache);
        $generatePackage  = new Processor\SkeletonFilesProcessor($this->env->skeleton(), $generatorFactory);

        return new Processor\ProcessorSequence($generateComposer, $generatePackage);
    }

    private function option(string $name, Source $default): Source
    {
        return isset($this->options[$name])
            ? new Source\PredefinedValue($this->options[$name])
            : $default;
    }

    private function interactive(string $prompt, Source $source): Source
    {
        return isset($this->options['i']) || isset($this->options['interactive'])
            ? new Source\InteractiveInput($prompt, $this->env->input(), $source)
            : $source;
    }
}
