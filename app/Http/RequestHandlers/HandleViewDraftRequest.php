<?php

namespace App\Http\RequestHandlers;

use App\Http\HtmlResponse;
use App\Http\HttpRequest;
use App\Http\HttpResponse;
use App\Http\RequestHandler;

class HandleViewDraftRequest extends RequestHandler
{
    public function handle(): HttpResponse
    {
        // @todo do better
        define('DRAFT_ID', $this->request->get('id'));
        return new HtmlResponse(
            require_once 'templates/draft.php'
        );
    }
}