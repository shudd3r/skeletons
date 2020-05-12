<?php

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
use Shudd3r\PackageFiles\Properties;


class PropertiesTest extends TestCase
{
    public function testInstantiation()
    {
        $this->assertInstanceOf(Properties::class, $this->properties());
    }

    /**
     * @dataProvider repositoryNamesFromUri
     *
     * @param string $url
     * @param string $name
     */
    public function testRepositoryNameIsResolvedFromRepositoryUrl(string $url, string $name)
    {
        $properties = $this->properties(['repositoryUrl' => $url]);
        $this->assertSame($name, $properties->repositoryName());
    }

    public function repositoryNamesFromUri()
    {
        return [
            ['', ''],
            ['https://github.com/repoUser/bar.git', 'repoUser/bar'],
            ['git@github.com:foo/repoName.git', 'foo/repoName']
        ];
    }

    private function properties(array $params = [])
    {
        return new Doubles\FakeProperties($params);
    }
}
