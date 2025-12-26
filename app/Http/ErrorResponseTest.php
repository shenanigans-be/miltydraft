<?php

declare(strict_types=1);

namespace App\Http;

use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ErrorResponseTest extends TestCase
{
    #[Test]
    public function itReturnsTheErrorAsJson(): void {
        $response = new ErrorResponse('foo');

        $this->assertSame(json_encode([
            'error' => 'foo',
        ]), $response->getBody());
    }
}