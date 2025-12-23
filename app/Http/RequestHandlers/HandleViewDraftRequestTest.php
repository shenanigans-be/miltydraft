<?php

namespace App\Http\RequestHandlers;

use App\Draft\Draft;
use App\Http\HttpRequest;
use App\Testing\MakesHttpRequests;
use App\Testing\RequestHandlerTestCase;
use App\Testing\TestCase;
use App\Testing\TestDrafts;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;

class HandleViewDraftRequestTest extends RequestHandlerTestCase
{
    use MakesHttpRequests;

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



        // cleanup
        app()->repository->delete($draft->id);
        dd($response);
    }

    #[Test]
    public function itReturnsAnErrorWhenDraftIsNotFound()
    {
        // @todo
    }

}