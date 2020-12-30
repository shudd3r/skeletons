<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token;

use Shudd3r\PackageFiles\Application\Token;


class OriginalContentsToken implements Token
{
    private OriginalContents $originalContents;

    public function __construct(OriginalContents $originalContents)
    {
        $this->originalContents = $originalContents;
    }

    public function replacePlaceholders(string $template): string
    {
        $clips = $this->originalContents->clips($template);

        $templateParts = explode(OriginalContents::PLACEHOLDER, $template);
        $generated     = array_shift($templateParts);
        foreach ($templateParts as $templatePart) {
            $generated .= array_shift($clips) . $templatePart;
        }

        return $generated;
    }
}
