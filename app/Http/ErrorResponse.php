<?php

namespace App\Http;

class ErrorResponse extends JsonResponse
{
    public function __construct(
        protected string $error,
        public int $code = 500,
    ) {
        parent::__construct([
            "error" => $this->error
        ], $code);
    }
}