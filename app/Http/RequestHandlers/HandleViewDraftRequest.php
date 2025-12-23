<?php

namespace App\Http\RequestHandlers;

use App\Draft\Exceptions\DraftRepositoryException;
use App\Http\ErrorResponse;
use App\Http\HtmlResponse;
use App\Http\HttpRequest;
use App\Http\HttpResponse;
use App\Http\RequestHandler;

class HandleViewDraftRequest extends RequestHandler
{
    public function handle(): HttpResponse
    {
        try {
            $draft = app()->repository->load($this->request->get('id'));
        } catch (DraftRepositoryException $e) {
            return new ErrorResponse('Draft not found', 404, true);
        }


        return $this->html(
            'templates/draft.php',
             [
                 'draft' => $draft
             ]
        );
    }
}