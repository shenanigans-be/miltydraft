<?php

declare(strict_types=1);

namespace App\Draft\Commands;

use App\Draft\Draft;
use App\Draft\Player;
use App\Draft\PlayerId;
use App\Shared\Command;

class UnclaimPlayer implements Command
{
    private Player $player;
    public function __construct(
        public Draft $draft,
        public PlayerId $playerId,
    )
    {
        $this->player = $this->draft->playerById($this->playerId);
    }

    public function handle(): void
    {
        $this->draft->players[$this->playerId->value] = $this->player->unclaim();
        $this->draft->secrets->removeSecretForPlayer($this->playerId);

        app()->repository->save($this->draft);
    }
}