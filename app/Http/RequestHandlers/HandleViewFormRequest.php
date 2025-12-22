<?php

namespace App\Http\RequestHandlers;

use App\Http\HtmlResponse;
use App\Http\HttpResponse;
use App\Http\RequestHandler;

class HandleViewFormRequest extends RequestHandler
{
    public function handle(): HttpResponse
    {
        return new HtmlResponse(
            require_once 'templates/generate.php'
        );
    }
}