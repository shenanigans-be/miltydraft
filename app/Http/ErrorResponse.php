<?php

namespace App\Http;

class ErrorResponse extends JsonResponse
{
    public function __construct(
        protected string $error,
    ) {
        parent::__construct([
            "error" => $this->error
        ]);
    }
}