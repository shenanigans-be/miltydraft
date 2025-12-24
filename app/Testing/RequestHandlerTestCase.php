<?php

namespace App\Testing;

use App\Application;
use App\Http\HttpRequest;
use App\Http\HttpResponse;
use App\Http\RequestHandlers\HandleViewDraftRequest;
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
}