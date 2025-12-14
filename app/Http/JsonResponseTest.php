<?php

namespace App\Http;

use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;

class JsonResponseTest extends TestCase
{
    #[Test]
    public function itReturnsTheDataAsJson() {
        $data = [
            "foo" => "bar"
        ];
        $response = new JsonResponse($data);
        $this->assertSame(json_encode($data), (string) $response);
    }
}