<?php

namespace App\Testing;

use App\Application;
use App\Draft\Draft;
use App\Http\HtmlResponse;
use App\Http\HttpRequest;
use App\Http\HttpResponse;
use App\Http\JsonResponse;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use \PHPUnit\Framework\TestCase as BaseTestCase;

abstract class RequestHandlerTestCase extends BaseTestCase
{
    protected string $requestHandlerClass = '';

    private Application $application;


    #[Before]
    public function setupApplication()
    {
        $this->application = new Application();
    }

    #[After]
    public function unsetApplication()
    {
        unset($this->application);
    }

    public function assertIsConfiguredAsHandlerForRoute($route)
    {
        $determinedHandler = $this->application->handlerForRequest($route);
        $this->assertInstanceOf($this->requestHandlerClass, $determinedHandler);
    }

    public function handleRequest($getParameters = [], $postParameters = [], $urlParameters = []): HttpResponse
    {
        $handler = new $this->requestHandlerClass(new HttpRequest($getParameters, $postParameters, $urlParameters));
        return $handler->handle();
    }

    public function assertJsonResponseSame(array $expected, HttpResponse $response)
    {
        $this->assertSame($expected, json_decode($response->getBody(), true));
    }

    public function assertResponseJson(HttpResponse $response)
    {
        $this->assertSame(JsonResponse::CONTENT_TYPE, $response->getContentType());
    }

    public function assertResponseHtml(HttpResponse $response)
    {
        $this->assertSame(HtmlResponse::CONTENT_TYPE, $response->getContentType());
    }

    public function assertResponseOk(HttpResponse $response)
    {
        $this->assertSame(200, $response->code);
    }
}