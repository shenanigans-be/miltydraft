<?php

declare(strict_types=1);

namespace App\Http;

class JsonResponse extends HttpResponse
{
    public const CONTENT_TYPE = 'application/json';

    public function __construct(
        protected array $data,
        public int $code = 200,
    ) {
        parent::__construct($this->code);
    }

    public function code(): int
    {
        return $this->code;
    }

    public function getBody(): string
    {
        return json_encode($this->data);
    }

    public function getContentType(): string
    {
        return 'application/json';
    }
}