<?php

namespace App\Http;

class ErrorResponse extends JsonResponse
{
    public function __construct(
        protected string $error,
        public int $code = 500,
        public bool $showErrorPage = false
    ) {
        parent::__construct([
            "error" => $this->error
        ], $code);
    }

    public function getBody(): string
    {
        if ($this->showErrorPage) {
            $error = $this->error;
            return include 'templates/error.php';
        } else {
            // return json
            return parent::getBody();
        }
    }

    public function getContentType(): string
    {
        if ($this->showErrorPage) {
            return include 'text/html';
        } else {
            return parent::getContentType();
        }
    }
}