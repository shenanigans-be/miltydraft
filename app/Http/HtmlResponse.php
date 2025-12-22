<?php

namespace App\Http;

class HtmlResponse extends HttpResponse
{
    public function __construct(
        protected string $html,
        public int $code = 200
    ) {
        parent::__construct($this->code);
    }

    public function code(): int
    {
        return $this->code;
    }

    public function getBody(): string
    {
        return $this->html;
    }
}