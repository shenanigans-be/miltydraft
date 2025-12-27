<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\Draft\Commands\PlayerPick;
use App\Draft\Pick;
use App\Draft\PickCategory;
use App\Draft\PlayerId;
use App\Http\HttpResponse;
use App\Http\JsonResponse;

class HandlePickRequest extends DraftRequestHandler
{
    public function handle(): HttpResponse
    {
        $draft = $this->loadDraftByUrlId('id');

        if ($draft == null) {
            return $this->error('Draft not found', 404);
        }

        $playerId = PlayerId::fromString($this->request->get('player'));

        $isAdmin = $draft->secrets->checkAdminSecret($this->request->get('admin'));

        if (! $isAdmin && ! $draft->secrets->checkPlayerSecret($playerId, $this->request->get('secret'))) {
            return $this->error('You are not allowed to do this.', 403);
        }

        if ($this->request->get('index', 0) != count($draft->log)) {
            return $this->error(
                'Draft data out of date, meaning: stuff has been picked or undone while this tab was open.',
                400,
            );
        }

        dispatch(new PlayerPick($draft, new Pick(
            $playerId,
            PickCategory::from($this->request->get('category')),
            $this->request->get('value'),
        )));

        return new JsonResponse([
            'draft' => $draft->toArray(),
            'success' => true,
        ]);
    }
}