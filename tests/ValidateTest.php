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

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Validate;
use Shudd3r\PackageFiles\Environment\Command;
use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Application\Command\Precondition;
use Shudd3r\PackageFiles\Application\Token\TokenCache;
use Shudd3r\PackageFiles\Replacement;


class ValidateTest extends TestCase
{
    public const PACKAGE_NAME  = 'package.name';
    public const PACKAGE_DESC  = 'package.description';
    public const SRC_NAMESPACE = 'namespace.src';
    public const REPO_NAME     = 'repository.name';

    public function testInitializedPackage_IsValid()
    {
        $files   = new Fixtures\ExampleFiles('example-files');
        $package = $files->directory('package-initialized');
        $env     = $this->envSetup($package, $files->directory('template'));

        $this->validateCommand($env)->execute();

        $this->assertSame(0, $env->output()->exitCode());
        $this->assertTrue($this->validatePrecondition($env)->isFulfilled());
    }

    public function testSynchronizedPackage_IsValid()
    {
        $files   = new Fixtures\ExampleFiles('example-files');
        $package = $files->directory('package-synchronized');
        $env     = $this->envSetup($package, $files->directory('template'));

        $this->validateCommand($env)->execute();

        $this->assertSame(0, $env->output()->exitCode());
        $this->assertTrue($this->validatePrecondition($env)->isFulfilled());
    }

    public function testDesynchronizedPackage_IsInvalid()
    {
        $files   = new Fixtures\ExampleFiles('example-files');
        $package = $files->directory('package-unsynchronized');
        $env     = $this->envSetup($package, $files->directory('template'));

        $this->validateCommand($env)->execute();

        $this->assertNotEquals(0, $env->output()->exitCode());
        $this->assertFalse($this->validatePrecondition($env)->isFulfilled());
    }

    //todo: failed precondition tests

    private function envSetup(
        Directory $package,
        Directory $skeleton
    ): RuntimeEnv {
        $terminal = new Doubles\MockedTerminal();
        $env = new RuntimeEnv($terminal, $terminal, $package, $skeleton);

        $replacements = $env->replacements();
        $replacements->add(self::PACKAGE_NAME, $packageName = new Replacement\PackageName($env));
        $replacements->add(self::REPO_NAME, new Replacement\RepositoryName($env, $packageName));
        $replacements->add(self::PACKAGE_DESC, new Replacement\PackageDescription($env, $packageName));
        $replacements->add(self::SRC_NAMESPACE, new Replacement\SrcNamespace($env, $packageName));

        $jsonMerge = fn (Template $template, File $composer) => new Template\MergedJsonTemplate($template, $composer);
        $env->addTemplate('composer.json', $jsonMerge);

        return $env;
    }

    private function validateCommand(RuntimeEnv $env): Command
    {
        $validate = new Validate($env);
        return $validate->command([]);
    }

    private function validatePrecondition(RuntimeEnv $env): Precondition
    {
        $validate = new Validate($env);
        return $validate->synchronizedSkeleton(new TokenCache());
    }
}
