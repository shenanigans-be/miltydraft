<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\Draft\Exceptions\DraftRepositoryException;
use App\Http\ErrorResponse;
use App\Http\HttpResponse;
use App\Http\JsonResponse;
use App\Http\RequestHandler;

class HandleGetDraftRequest extends RequestHandler
{
    public function handle(): HttpResponse
    {
        try {
            $draft = app()->repository->load($this->request->get('id'));
        } catch (DraftRepositoryException $e) {
            return new ErrorResponse('Draft not found', 404);
        }

        return new JsonResponse(
            $draft->toArray(),
        );
    }
}