<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Template;

use Shudd3r\PackageFiles\Template;
use Shudd3r\PackageFiles\Application\FileSystem\File;
use Shudd3r\PackageFiles\Properties;
use InvalidArgumentException;


class FileTemplate implements Template
{
    private const TOKENS = [
        '{REPO_URL}'     => 'repositoryUrl',
        '{REPO_NAME}'    => 'repositoryName',
        '{PACKAGE_NAME}' => 'packageName',
        '{PACKAGE_DESC}' => 'packageDescription',
        '{PACKAGE_NS}'   => 'sourceNamespace'
    ];

    private File $templateFile;

    public function __construct(File $templateFile)
    {
        if (!$templateFile->exists()) {
            throw new InvalidArgumentException();
        }
        $this->templateFile = $templateFile;
    }

    public function render(Properties $properties): string
    {
        $contents = $this->templateFile->contents();
        if (!$contents) { return ''; }

        foreach (self::TOKENS as $token => $valueName) {
            $value    = $properties->{$valueName}();
            $contents = str_replace($token, $value, $contents);
        }

        return $contents;
    }
}
