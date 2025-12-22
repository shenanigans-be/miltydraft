<?php

namespace App\Http\RequestHandlers;

use App\Http\HtmlResponse;
use App\Http\HttpResponse;
use App\Http\RequestHandler;

class HandleTestRequest extends RequestHandler
{
    public function handle(): HttpResponse
    {
        return new HtmlResponse("test");
    }
}