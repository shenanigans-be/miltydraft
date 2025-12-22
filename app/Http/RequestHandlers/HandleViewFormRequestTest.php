<?php

namespace App\Http\RequestHandlers;

use App\Testing\MakesHttpRequests;
use App\Testing\RequestHandlerTestCase;
use PHPUnit\Framework\Attributes\Test;

class HandleViewFormRequestTest extends RequestHandlerTestCase
{
    use MakesHttpRequests;

    protected string $requestHandlerClass = HandleViewFormRequest::class;

    #[Test]
    public function itIsConfiguredAsRouteHandler()
    {
        $this->assertIsConfiguredAsHandlerForRoute('/');
    }
}