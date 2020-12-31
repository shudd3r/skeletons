<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Template;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Template\ExpandedTokenTemplate;
use Shudd3r\PackageFiles\Application\Token\CompositeToken;
use Shudd3r\PackageFiles\Tests\Doubles;


class ExpandedTokenTemplateTest extends TestCase
{
    public function testSubsequentTemplateReceivesExpandedToken()
    {
        $newToken = new Doubles\FakeToken('bar');
        $template = new Doubles\FakeTemplate('rendered');
        $expanded = new ExpandedTokenTemplate($newToken, $template);

        $token = new Doubles\FakeToken('foo');
        $expanded->render($token);
        $this->assertEquals(new CompositeToken($token, $newToken), $template->receivedToken);
    }
}
