<?php

declare(strict_types=1);

namespace App\Draft\Commands;

use App\Draft\Draft;
use App\Shared\Command;

class UndoLastPick implements Command
{
    public function __construct(
        public Draft $draft,
    ) {
    }

    public function handle(): Draft
    {
        if (empty($this->draft->log)) {
            throw new \Exception('Cannot undo pick, draft has not started');
        }

        $lastPick = array_pop($this->draft->log);
        $player = $this->draft->playerById($lastPick->playerId);
        $this->draft->updatePlayerData($player->unpick($lastPick->category));

        $this->draft->updateCurrentPlayer();

        app()->repository->save($this->draft);

        return $this->draft;
    }
}