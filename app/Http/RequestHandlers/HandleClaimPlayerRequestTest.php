<?php

namespace App\Http\RequestHandlers;

use App\Testing\RequestHandlerTestCase;
use PHPUnit\Framework\Attributes\Test;

class HandleClaimPlayerRequestTest extends RequestHandlerTestCase
{

    protected string $requestHandlerClass = HandleClaimPlayerRequest::class;

    #[Test]
    public function itIsConfiguredAsRouteHandler()
    {
        $this->assertIsConfiguredAsHandlerForRoute('/api/draft/123/claim');
    }


    #[Test]
    public function itReturnsJson()
    {
        $response = $this->handleRequest([], [], ['id' => '123']);
        $this->assertResponseOk($response);
        $this->assertResponseJson($response);
    }

    #[Test]
    public function itReturnsErrorIfDraftNotFound()
    {

        $response = $this->handleRequest([], [], ['id' => '123']);
        $this->assertResponseOk($response);
        $this->assertResponseJson($response);
        $this->assertJsonResponseSame(['error' => 'Draft not found'], $response);
    }

    #[Test]
    public function itReturnsErrorIfPlayerAlreadyClaimed()
    {

    }

    #[Test]
    public function itCanClaimAPlayer()
    {

    }
}