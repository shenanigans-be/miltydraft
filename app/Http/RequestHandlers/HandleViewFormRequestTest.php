<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\Testing\RequestHandlerTestCase;
use PHPUnit\Framework\Attributes\Test;

class HandleViewFormRequestTest extends RequestHandlerTestCase
{

    protected string $requestHandlerClass = HandleViewFormRequest::class;

    #[Test]
    public function itIsConfiguredAsRouteHandler(): void
    {
        $this->assertIsConfiguredAsHandlerForRoute('/');
    }

    #[Test]
    public function itReturnsTheForm(): void
    {
        $response = $this->handleRequest();

        $this->assertResponseHtml($response);
        $this->assertResponseOk($response);
    }
}