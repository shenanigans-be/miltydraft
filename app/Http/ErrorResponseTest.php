<?php

namespace App\Http;

use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ErrorResponseTest extends TestCase
{
    #[Test]
    public function itReturnsTheErrorAsJson() {
        $response = new ErrorResponse("foo");

        $this->assertSame(json_encode([
            "error" => "foo"
        ]), $response->getBody());
    }
}