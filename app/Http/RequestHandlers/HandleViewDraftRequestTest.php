<?php

namespace App\Http\RequestHandlers;

use App\Draft\Draft;
use App\Http\ErrorResponse;
use App\Http\HtmlResponse;
use App\Http\HttpRequest;
use App\Testing\MakesHttpRequests;
use App\Testing\RequestHandlerTestCase;
use App\Testing\TestCase;
use App\Testing\TestDrafts;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;

class HandleViewDraftRequestTest extends RequestHandlerTestCase
{
    protected string $requestHandlerClass = HandleViewDraftRequest::class;

    #[Test]
    public function itIsConfiguredAsRouteHandler()
    {
        $this->assertIsConfiguredAsHandlerForRoute('/d/123');
    }

    #[Test]
    #[DataProviderExternal(TestDrafts::class, 'provideTestDrafts')]
    public function itDisplaysADraft($data)
    {
        $draft = Draft::fromJson($data);

        app()->repository->save($draft);

        $handler = new HandleViewDraftRequest(new HttpRequest([], [], ['id' => (string) $draft->id]));
        $response = $handler->handle();

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertSame($response->code, 200);

        // cleanup
        app()->repository->delete($draft->id);
    }

    #[Test]
    public function itReturnsAnErrorWhenDraftIsNotFound()
    {
        $handler = new HandleViewDraftRequest(new HttpRequest([], [], ['id' => '1234']));
        $response = $handler->handle();
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertSame($response->code, 404);
    }

}