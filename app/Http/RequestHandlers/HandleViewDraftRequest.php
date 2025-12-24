<?php

namespace App\Http\RequestHandlers;

use App\Draft\Exceptions\DraftRepositoryException;
use App\Http\ErrorResponse;
use App\Http\HtmlResponse;
use App\Http\HttpRequest;
use App\Http\HttpResponse;
use App\Http\RequestHandler;

class HandleViewDraftRequest extends DraftRequestHandler
{
    public function handle(): HttpResponse
    {
        $draft = $this->loadDraftByUrlId();

        if ($draft == null) {
            return $this->error('Draft not found', 404, true);
        }

        return $this->html(
            'templates/draft.php',
             [
                 'draft' => $draft
             ]
        );
    }
}