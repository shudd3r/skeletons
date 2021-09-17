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

use Shudd3r\PackageFiles\Application;


class UpdateTest extends IntegrationTestCase
{
    private array $options = [
        'repo'    => 'updated/repo',
        'package' => 'updated/package-name',
        'desc'    => 'Updated package description',
        'ns'      => 'Package\Updated'
    ];

    public function testSynchronizedPackage_IsUpdated()
    {
        $env = $this->envSetup('package-synchronized');
        $app = new Application($env);

        $app->run('update', $this->options);
        $this->assertSameFiles($env, 'package-updated');
    }

    public function testPackageWithoutMetaDataFile_IsNotUpdated()
    {
        $env = $this->envSetup('package-synchronized', new Doubles\MockedFile(null));
        $app = new Application($env);

        $app->run('update', $this->options);
        $this->assertSameFiles($env, 'package-synchronized');
    }

    public function testDesynchronizedPackage_IsNotUpdated()
    {
        $env = $this->envSetup('package-desynchronized');
        $app = new Application($env);

        $app->run('update', $this->options);
        $this->assertSameFiles($env, 'package-desynchronized');
    }
}
