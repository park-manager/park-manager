<?php

declare(strict_types=1);

/*
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace ParkManager\Tests\UI\Web\Response;

use ParkManager\UI\Web\Response\TwigResponse;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class TwigResponseTest extends TestCase
{
    /** @test */
    public function it_is_constructable(): void
    {
        $response = new TwigResponse('client/show_user.html.twig', ['foo' => 'bar']);

        static::assertSame(200, $response->getStatusCode());
        static::assertSame('client/show_user.html.twig', $response->getTemplate());
        static::assertSame(['foo' => 'bar'], $response->getTemplateVariables());
    }

    /** @test */
    public function it_is_constructable_with_a_custom_status_code(): void
    {
        $response = new TwigResponse('client/show_user.html.twig', ['foo' => 'bar'], 400);

        static::assertSame(400, $response->getStatusCode());
        static::assertSame('client/show_user.html.twig', $response->getTemplate());
        static::assertSame(['foo' => 'bar'], $response->getTemplateVariables());
    }

    /** @test */
    public function it_is_constructable_with_custom_headers(): void
    {
        $response = new TwigResponse('client/show_user.html.twig', ['foo' => 'bar'], 200, ['X-Foo' => 'bar']);

        static::assertEquals('bar', $response->headers->get('X-Foo'));
    }
}