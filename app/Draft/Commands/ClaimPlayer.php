<?php

declare(strict_types=1);

namespace App\Draft\Commands;

use App\Draft\Draft;
use App\Draft\PlayerId;
use App\Shared\Command;

class ClaimPlayer implements Command
{
    public function __construct(
        public Draft $draft,
        public PlayerId $playerId,
    )
    {
    }

    public function handle(): string
    {
        $player = $this->draft->playerById($this->playerId);
        $this->draft->players[$this->playerId->value] = $player->claim();
        $secret = $this->draft->secrets->generateSecretForPlayer($this->playerId);

        app()->repository->save($this->draft);

        return $secret;
    }
}