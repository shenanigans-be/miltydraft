<?php

namespace App\Http\RequestHandlers;

use App\Http\HtmlResponse;
use App\Http\HttpResponse;
use App\Http\RequestHandler;

class HandleViewFormRequest extends RequestHandler
{
    public function handle(): HttpResponse
    {
        return $this->html('templates/generate.php');
    }
}