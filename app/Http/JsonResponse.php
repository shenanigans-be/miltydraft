<?php

namespace App\Http;

class JsonResponse extends HttpResponse
{
    public function __construct(
        protected array $data
    ) {

    }

    public function __toString()
    {
        return json_encode($this->data);
    }
}