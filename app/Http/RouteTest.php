<?php

declare(strict_types=1);

namespace App\Http;

use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RouteTest extends TestCase
{
    #[Test]
    public function itCanMatchARoute(): void
    {
        $route = new Route('/hello/world', 'SomeClass');

        $result = $route->match('/hello/world');

        $this->assertNotNull($result);
        $this->assertSame('SomeClass', $result->requestHandlerClass);
        $this->assertEmpty($result->requestParameters);
    }

    #[Test]
    public function itCanCaptureUrlParameters(): void
    {
        $route = new Route('/slug/{foo}/edit/{bar}', 'SomeClass');

        $result = $route->match('/slug/123/edit/abc1234');

        $this->assertSame('SomeClass', $result->requestHandlerClass);
        $this->assertSame('123', $result->requestParameters['foo']);
        $this->assertSame('abc1234', $result->requestParameters['bar']);
    }

    #[Test]
    public function itCanDealWithATrailingSlash(): void
    {
        $route = new Route('/slug/{id}', 'SomeClass');

        $result = $route->match('/slug/123/');

        $this->assertSame('SomeClass', $result->requestHandlerClass);
        $this->assertSame('123', $result->requestParameters['id']);
    }

    #[Test]
    public function itCanRouteToIndex(): void
    {
        $route = new Route('/', 'IndexClass');

        $result = $route->match('/');

        $this->assertSame('IndexClass', $result->requestHandlerClass);
    }
}