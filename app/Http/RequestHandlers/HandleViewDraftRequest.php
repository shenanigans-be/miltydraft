<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\Http\HttpResponse;

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
                 'draft' => $draft,
             ],
        );
    }
}