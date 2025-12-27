<?php

declare(strict_types=1);

namespace App\Draft\Commands;

use App\Draft\Draft;
use App\Draft\Exceptions\InvalidPickException;
use App\Draft\Pick;
use App\Shared\Command;

class PlayerPick implements Command
{
    public function __construct(
        public Draft $draft,
        public Pick $pick,
    ) {
    }

    public function handle(): Draft
    {
        $player = $this->draft->playerById($this->pick->playerId);

        if ($player->id->value !== $this->draft->currentPlayerId->value) {
            throw InvalidPickException::notPlayersTurn();
        }

        foreach($this->draft->players as $p) {
            if ($p->getPick($this->pick->category) == $this->pick->pickedOption) {
                throw InvalidPickException::optionAlreadyPicked($this->pick->pickedOption);
            }
        }

        $this->draft->updatePlayerData($player->pick($this->pick));

        $this->draft->log[] = $this->pick;
        $this->draft->updateCurrentPlayer();

        app()->repository->save($this->draft);

        return $this->draft;
    }
}