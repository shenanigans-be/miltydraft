<?php

namespace App\Http\RequestHandlers;

use App\Testing\RequestHandlerTestCase;
use PHPUnit\Framework\Attributes\Test;

class HandleViewFormRequestTest extends RequestHandlerTestCase
{

    protected string $requestHandlerClass = HandleViewFormRequest::class;

    #[Test]
    public function itIsConfiguredAsRouteHandler()
    {
        $this->assertIsConfiguredAsHandlerForRoute('/');
    }


    #[Test]
    public function itReturnsTheForm()
    {
        $response = $this->handleRequest();

        $this->assertResponseHtml($response);
        $this->assertResponseOk($response);
    }
}