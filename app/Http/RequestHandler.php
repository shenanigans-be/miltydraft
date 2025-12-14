<?php

namespace App\Http;

abstract class RequestHandler
{
    public abstract function handle(HttpRequest $request): HttpResponse;

    protected function error(string $error): ErrorResponse
    {
        return new ErrorResponse($error);
    }
}