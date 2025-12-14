<?php

namespace App\Http;

class HttpRequest
{
    public function __construct(
        protected array $getParameters,
        protected array $postParameters
    ) {

    }

    public static function fromRequest(): self
    {
        return new self(
            $_GET,
            $_POST
        );
    }

    public function get($key, $defaultValue = null)
    {
        if (isset($this->getParameters[$key])) {
            return $this->getParameters[$key];
        }
        if (isset($this->postParameters[$key])) {
            return $this->postParameters[$key];
        }
        return $defaultValue;
    }
}