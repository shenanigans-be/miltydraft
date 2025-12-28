<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\Http\HtmlResponse;
use App\Http\HttpRequest;
use App\Testing\RequestHandlerTestCase;
use App\Testing\UsesTestDraft;
use PHPUnit\Framework\Attributes\Test;

class HandleViewDraftRequestTest extends RequestHandlerTestCase
{
    use UsesTestDraft;

    protected string $requestHandlerClass = HandleViewDraftRequest::class;

    #[Test]
    public function itIsConfiguredAsRouteHandler(): void
    {
        $this->assertIsConfiguredAsHandlerForRoute('/d/123');
    }

    #[Test]
    public function itCanFetchDraft(): void
    {
        $handler = new HandleViewDraftRequest(new HttpRequest([], ['id' => $this->testDraft->id], []));

        $response = $handler->handle();

        $this->assertSame(200, $response->code);
        $this->assertNotSame(HtmlResponse::CONTENT_TYPE, $response->code);
    }

    #[Test]
    public function itShowsAnErrorPageWhenDraftIsNotFound(): void
    {
        $handler = new HandleViewDraftRequest(new HttpRequest([], ['id' => '123'], []));
        $response = $handler->handle();

        $this->assertSame(404, $response->code);
        $this->assertNotSame(HtmlResponse::CONTENT_TYPE, $response->code);
    }

}