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

    /**
     * @todo put this somewhere else
     */
    public static function renderTemplate($template, $data): string
    {
        ob_start();
        extract($data);
        include $template;
        return ob_get_clean();
    }

    public function getContentType(): string
    {
        return 'text/html; charset=UTF-8';
    }
}