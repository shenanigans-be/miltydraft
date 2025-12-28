<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\Http\HttpResponse;
use App\Http\JsonResponse;

class HandleGetDraftRequest extends DraftRequestHandler
{
    public function handle(): HttpResponse
    {
        $draft = $this->loadDraftByUrlId('id');

        if ($draft == null) {
            return $this->error('Draft not found', 404);
        }

        return new JsonResponse(
            $draft->toArray(),
        );
    }
}