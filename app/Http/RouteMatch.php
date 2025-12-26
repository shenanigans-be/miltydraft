<?php

declare(strict_types=1);

namespace App\Http;

class RouteMatch
{
    public function __construct(
        public readonly string $requestHandlerClass,
        public readonly array $requestParameters,
    ) {
    }
}