<?php

declare(strict_types=1);

namespace App\Draft\Commands;

use App\Draft\Draft;
use App\Draft\PlayerId;
use App\Draft\Settings;
use App\Shared\Command;

/**
 * Updates the settings and player names. Does not regenerate the slices or factions.
 */
class UpdateDraftSettings implements Command
{
    public function __construct(
        public Draft $draft,
        public Settings $settings,
        /** @var array<string, string> $playerNames keyed by player id */
        public array $playerNames,
    ) {
    }

    public function handle(): Draft
    {
        if (! $this->draft->canRegenerate()) {
            throw new \Exception('Cannot change settings once the draft has started');
        }

        foreach ($this->playerNames as $id => $name) {
            $player = $this->draft->playerById(PlayerId::fromString((string) $id));
            $this->draft->updatePlayerData($player->rename($name));
        }

        $this->draft->settings = $this->settings;

        app()->repository->save($this->draft);

        return $this->draft;
    }
}
