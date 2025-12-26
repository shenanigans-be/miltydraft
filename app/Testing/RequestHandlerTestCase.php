<?php

declare(strict_types=1);

namespace App\Testing;

use \PHPUnit\Framework\TestCase as BaseTestCase;
use App\Application;
use App\Http\HtmlResponse;
use App\Http\HttpRequest;
use App\Http\HttpResponse;
use App\Http\JsonResponse;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;

abstract class RequestHandlerTestCase extends BaseTestCase
{
    protected string $requestHandlerClass = '';

    private Application $application;

    #[Before]
    public function setupApplication(): void
    {
        $this->application = new Application();
    }

    #[After]
    public function unsetApplication(): void
    {
        unset($this->application);
    }

    public function assertIsConfiguredAsHandlerForRoute($route): void
    {
        $determinedHandler = $this->application->handlerForRequest($route);
        $this->assertInstanceOf($this->requestHandlerClass, $determinedHandler);
    }

    public function handleRequest($getParameters = [], $postParameters = [], $urlParameters = []): HttpResponse
    {
        $handler = new $this->requestHandlerClass(new HttpRequest($getParameters, $postParameters, $urlParameters));

        return $handler->handle();
    }

    public function assertJsonResponseSame(array $expected, HttpResponse $response): void
    {
        $this->assertSame($expected, json_decode($response->getBody(), true));
    }

    public function assertResponseContentType(string $expected, HttpResponse $response): void
    {
        $this->assertSame($expected, $response->getContentType());
    }

    public function assertResponseJson(HttpResponse $response): void
    {
        $this->assertResponseContentType(JsonResponse::CONTENT_TYPE, $response);
    }

    public function assertResponseHtml(HttpResponse $response): void
    {
        $this->assertResponseContentType(HtmlResponse::CONTENT_TYPE, $response);
    }

    public function assertResponseCode(int $expected, HttpResponse $response): void
    {
        $this->assertSame($expected, $response->code);
    }

    public function assertResponseOk(HttpResponse $response): void
    {
        $this->assertResponseCode(200, $response);
    }

    public function assertResponseNotFound(HttpResponse $response): void
    {
        $this->assertResponseCode(404, $response);
    }

    public function assertForbidden(HttpResponse $response): void
    {
        $this->assertResponseCode(403, $response);
    }
}