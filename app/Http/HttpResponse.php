<?php

namespace App\Http;

abstract class HttpResponse
{
    public function __construct(
        public int $code
    ) {

    }
    abstract public function getBody(): string;
}