<?php

declare(strict_types=1);

namespace App\Http;

use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;

class JsonResponseTest extends TestCase
{
    #[Test]
    public function itReturnsTheDataAsJson(): void {
        $data = [
            'foo' => 'bar',
        ];
        $response = new JsonResponse($data);
        $this->assertSame(json_encode($data), $response->getBody());
    }
}