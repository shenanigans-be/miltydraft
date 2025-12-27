<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\Draft\Commands\UndoLastPick;
use App\Http\HttpResponse;
use App\Http\JsonResponse;

class HandleUndoRequest extends DraftRequestHandler
{
    public function handle(): HttpResponse
    {
        $draft = $this->loadDraftByUrlId();

        if ($draft == null) {
            return $this->error('Draft not found', 404);
        }

        $adminSecret = $this->request->get('admin');

        if (! $draft->secrets->checkAdminSecret($adminSecret)) {
            return $this->error('Only the admin can undo picks', 403);
        }

        dispatch(new UndoLastPick($draft));

        return new JsonResponse([
            'draft' => $draft->toArray(),
            'success' => true,
        ]);
    }
}