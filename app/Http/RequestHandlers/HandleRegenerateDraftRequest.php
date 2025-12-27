<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\Draft\Commands\RegenerateDraft;
use App\Http\HttpResponse;

class HandleRegenerateDraftRequest extends DraftRequestHandler
{
    public function handle(): HttpResponse
    {
        $draft = $this->loadDraftByUrlId();

        if ($draft == null) {
            return $this->error('Draft not found', 404);
        }

        $adminSecret = $this->request->get('admin');

        if (! $draft->secrets->checkAdminSecret($adminSecret)) {
            return $this->error('Only the admin can regenerate', 403);
        }

        dispatch(new RegenerateDraft(
            $draft,
            $this->request->get('slices', false) === 'true',
            $this->request->get('factions', false) === 'true',
            $this->request->get('order', false) === 'true',
        ));

        return $this->json([
            'draft' => $draft->toArray(),
        ]);
    }
}