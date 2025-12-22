<?php

namespace App\Http;

class RouteMatch
{
    public function __construct(
        public readonly string $requestHandlerClass,
        public readonly array $requestParameters
    ) {
    }
}