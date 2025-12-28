<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\Testing\RequestHandlerTestCase;
use App\Testing\UsesTestDraft;
use PHPUnit\Framework\Attributes\Test;

class HandleGetDraftRequestTest extends RequestHandlerTestCase
{
    use UsesTestDraft;

    protected string $requestHandlerClass = HandleGetDraftRequest::class;

    #[Test]
    public function itIsConfiguredAsRouteHandler(): void
    {
        $this->assertIsConfiguredAsHandlerForRoute('/api/draft/123');
    }

    #[Test]
    public function itReturnsErrorIfDraftNotFound(): void
    {
        $response = $this->handleRequest(['id' => '12344']);

        $this->assertSame(404, $response->code);
        $this->assertResponseJson($response);
        $this->assertJsonResponseSame(['error' => 'Draft not found'], $response);
    }

    #[Test]
    public function itCanReturnDraftData(): void
    {
        $response = $this->handleRequest(['id' => $this->testDraft->id]);

        $this->assertSame(200, $response->code);
        $this->assertResponseJson($response);
    }
}