<?php

declare(strict_types=1);

namespace App\Http;

class ErrorResponse extends JsonResponse
{
    public function __construct(
        protected string $error,
        public int $code = 500,
        public bool $showErrorPage = false,
    ) {
        parent::__construct([
            'error' => $this->error,
        ], $code);
    }

    public function getBody(): string
    {
        if ($this->showErrorPage) {
            return HtmlResponse::renderTemplate('templates/error.php', [
                'error' => $this->error,
            ]);
        } else {
            // return json
            return parent::getBody();
        }
    }

    public function getContentType(): string
    {
        if ($this->showErrorPage) {
            return 'text/html';
        } else {
            return parent::getContentType();
        }
    }
}