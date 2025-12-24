<?php

namespace App\Http\RequestHandlers;

use App\Http\HtmlResponse;
use App\Http\HttpRequest;
use App\Testing\RequestHandlerTestCase;
use App\Testing\UsesTestDraft;
use PHPUnit\Framework\Attributes\Test;

class HandleGetDraftRequestTest extends RequestHandlerTestCase
{
    use UsesTestDraft;

    protected string $requestHandlerClass = HandleGetDraftRequest::class;

    #[Test]
    public function itIsConfiguredAsRouteHandler()
    {
        $this->assertIsConfiguredAsHandlerForRoute('/api/draft/123');
    }

    #[Test]
    public function itReturnsErrorIfDraftNotFound()
    {
        $response = $this->handleRequest(['id' => '12344']);

        $this->assertSame(404, $response->code);
        $this->assertResponseJson($response);
        $this->assertJsonResponseSame(['error' => 'Draft not found'], $response);
    }

    #[Test]
    public function itCanReturnDraftData()
    {
        $response = $this->handleRequest(['id' => $this->testDraft->id]);

        $this->assertSame(200, $response->code);
        $this->assertResponseJson($response);
    }
}