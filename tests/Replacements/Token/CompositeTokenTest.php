<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Replacements\Token;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Replacements\Token\CompositeToken;
use Shudd3r\Skeletons\Replacements\Token\BasicToken;


class CompositeTokenTest extends TestCase
{
    public function test_replace_is_applied_to_all_internal_tokens(): void
    {
        $token = new CompositeToken(
            new BasicToken('foo.token', 'foo'),
            new BasicToken('bar.token', 'bar'),
            new BasicToken('baz.token', 'baz')
        );
        $template = "Template with {foo.token}-{bar.token}-{baz.token}";

        $this->assertSame('Template with foo-bar-baz', $token->replace($template));
    }

    public function test_for_default_instance_value_method_returns_null(): void
    {
        $token = new CompositeToken(new BasicToken('foo', 'foo-value'));
        $this->assertNull($token->value());
    }

    public function test_for_instance_with_value_token_value_method_returns_value_of_first_token(): void
    {
        $token = CompositeToken::withValueToken(
            new BasicToken('foo', 'foo-value'),
            new BasicToken('bar', 'bar-value')
        );
        $this->assertSame('foo-value', $token->value());
    }
}
