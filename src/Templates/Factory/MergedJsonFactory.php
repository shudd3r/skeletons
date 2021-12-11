<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Templates\Factory;

use Shudd3r\Skeletons\Templates\Factory;
use Shudd3r\Skeletons\Templates\Template;
use Shudd3r\Skeletons\Templates\Contents;


class MergedJsonFactory implements Factory
{
    private bool $dynamicKeyUpdate;

    /**
     * This parameter should be set to true only for updating templates
     * with dynamic keys (containing placeholders) when valid schema
     * can be assumed.
     *
     * Otherwise, merging algorithm would not be able to distinguish
     * missing key in original file (with additional key that is not
     * present in the template schema) from updated key that should
     * replace the old one.
     *
     * When synchronization can be assumed algorithm can treat keys
     * positionally recognizing mismatched key at current position
     * as an old value of updated schema that should be replaced.
     *
     * @param bool $dynamicKeyUpdate
     */
    public function __construct(bool $dynamicKeyUpdate = false)
    {
        $this->dynamicKeyUpdate = $dynamicKeyUpdate;
    }

    public function template(Contents $contents): Template
    {
        return new Template\MergedJsonTemplate(
            new Template\BasicTemplate($contents->template()),
            $contents->package(),
            $this->dynamicKeyUpdate
        );
    }
}
