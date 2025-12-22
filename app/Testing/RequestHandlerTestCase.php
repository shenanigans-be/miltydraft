<?php

namespace App\Testing;

use App\Application;
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
}