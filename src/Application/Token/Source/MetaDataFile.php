<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token\Source;

use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Application\Token\Parser;
use RuntimeException;


class MetaDataFile implements Source
{
    private File   $metaFile;
    private Source $fallback;
    private array  $metaData;

    public function __construct(File $metaFile, Source $fallback)
    {
        $this->metaFile = $metaFile;
        $this->fallback = $fallback;
    }

    public function value(Parser $parser): string
    {
        isset($this->metaData) or $this->metaData = $this->fileMetaData();

        $parserClass = get_class($parser);
        return $this->metaData[$parserClass] ?? $this->fallback->value($parser);
    }

    private function fileMetaData()
    {
        $data = json_decode($this->metaFile->contents(), true);
        if (!$data || !is_array($data)) {
            throw new RuntimeException();
        }

        return $data;
    }
}
