<?php

namespace App\Http\RequestHandlers;

use App\Testing\MakesHttpRequests;
use App\Testing\RequestHandlerTestCase;
use PHPUnit\Framework\Attributes\Test;

class HandleClaimPlayerRequestTest extends RequestHandlerTestCase
{
    use MakesHttpRequests;

    protected string $requestHandlerClass = HandleClaimPlayerRequest::class;

    #[Test]
    public function itIsConfiguredAsRouteHandler()
    {
        $this->assertIsConfiguredAsHandlerForRoute('/api/draft/123/claim');
    }
}