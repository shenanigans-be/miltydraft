<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\Http\HttpResponse;

class HandleRestoreClaimRequest extends DraftRequestHandler
{
    public function handle(): HttpResponse
    {
        $draft = $this->loadDraftByUrlId('draft');

        // @todo this should be in some shared thing for all DraftRequestHandlers
        if ($draft == null) {
            return $this->error('Draft not found', 404);
        }

        $secret = $this->request->get('secret', '');

        if ($draft->secrets->checkAdminSecret($secret)) {
            return $this->json([
                'admin' => $secret,
                'success' => true,
            ]);
        }

        $playerId = $draft->secrets->playerIdBySecret($secret);

        if ($playerId == null) {
            return $this->error('No player with that secret', 403);
        } else {
            return $this->json([
                'player' => $playerId->value,
                'secret' => $secret,
                'success' => true,
            ]);
        }
    }
}