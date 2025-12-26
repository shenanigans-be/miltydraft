<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\Draft\Commands\ClaimPlayer;
use App\Draft\Commands\UnclaimPlayer;
use App\Draft\Exceptions\DraftRepositoryException;
use App\Draft\PlayerId;
use App\Http\ErrorResponse;
use App\Http\HttpResponse;

class HandleClaimOrUnclaimPlayerRequest extends DraftRequestHandler
{
    public function handle(): HttpResponse
    {
        try {
            $draft = $this->loadDraftByUrlId('draft');

            if ($draft == null) {
                return $this->error('Draft not found', 404);
            }

            $playerId = PlayerId::fromString($this->request->get('player'));
            $unclaim = $this->request->get('unclaim') == 1;

            $player = $draft->playerById($playerId);

            if ($unclaim) {
                dispatch(new UnclaimPlayer($draft, $playerId));
            } else {
                $secret = dispatch(new ClaimPlayer($draft, $playerId));
            }

            return $this->json([
                'draft' => $draft->toArray(),
                'player' => $playerId->value,
                'success' => true,
                'secret' => $unclaim ? null : $secret,
            ]);

        } catch (DraftRepositoryException $e) {
            return new ErrorResponse('Draft not found', 404);
        }
    }
}