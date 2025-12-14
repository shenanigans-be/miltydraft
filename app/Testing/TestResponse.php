<?php

namespace App\Testing;

use http\Env\Response;
use PHPUnit\Event\Runtime\PHPUnit;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\ResponseInterface;

class TestResponse
{
    public function __construct(
        private ResponseInterface $response
    ) {

    }

     public function assertOk()
     {
        Assert::assertSame($this->response->getStatusCode(), 200);
     }
}