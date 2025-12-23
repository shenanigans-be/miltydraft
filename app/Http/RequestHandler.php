<?php

namespace App\Http;

abstract class RequestHandler
{
    public function __construct(
        protected HttpRequest $request
    ) {
    }

    public abstract function handle(): HttpResponse;

    protected function error(string $error): ErrorResponse
    {
        return new ErrorResponse($error);
    }

    public function json($data = [], $code = 200): JsonResponse
    {
        return new JsonResponse($data, $code);
    }
}