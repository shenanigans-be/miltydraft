<?php

namespace App\Http;

class HttpRequest
{
    public function __construct(
        protected readonly array $getParameters,
        protected readonly array $postParameters,
        protected readonly array $urlParameters
    ) {
    }

    public static function fromRequest($urlParameters = []): self
    {
        return new self(
            $_GET,
            $_POST,
            $urlParameters
        );
    }

    public function get($key, $defaultValue = null)
    {
        if (isset($this->urlParameters[$key])) {
            return $this->urlParameters[$key];
        }
        if (isset($this->getParameters[$key])) {
            return $this->getParameters[$key];
        }
        if (isset($this->postParameters[$key])) {
            return $this->postParameters[$key];
        }
        return $defaultValue;
    }
}