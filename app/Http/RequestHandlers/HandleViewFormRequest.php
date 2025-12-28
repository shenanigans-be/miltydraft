<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\Http\HttpResponse;
use App\Http\RequestHandler;

class HandleViewFormRequest extends RequestHandler
{
    public function handle(): HttpResponse
    {
        return $this->html('templates/generate.php');
    }
}