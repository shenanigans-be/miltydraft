<?php

declare(strict_types=1);

namespace App\Draft\Commands;

use App\Draft\Draft;
use App\Draft\DraftId;
use App\Draft\Player;
use App\Draft\PlayerId;
use App\Draft\Secrets;
use App\Draft\Settings;
use App\Shared\Command;
use App\TwilightImperium\AllianceTeamMode;

class GenerateDraft implements Command
{
    public function __construct(
        private readonly Settings $settings,
    )
    {
    }

    public function handle(): Draft
    {
        $players = $this->generatePlayerData();

        // not going through dispatch method because if we're faking it then that sucks
        $slices = (new GenerateSlicePool($this->settings))->handle();
        $factions = (new GenerateFactionPool($this->settings))->handle();

        return new Draft(
            DraftId::generate(),
            false,
            $players,
            $this->settings,
            $this->generateSecrets(),
            $slices,
            $factions,
            [],
            PlayerId::fromString(array_key_first($players)),
        );
    }

    protected function generateSecrets(): Secrets
    {
        return new Secrets(Secrets::generateSecret());
    }

    /**
     * @return array<string>
     */
    protected function generateTeamNames(): array
    {
        return array_slice(['A', 'B', 'C', 'D'], 0, count($this->settings->playerNames) / 2);
    }

    /**
     * @return array<string, Player>
     */
    public function generatePlayerData(): array
    {
        /** @var array<string, Player> $players */
        $players = [];

        $playerNames = [...$this->settings->playerNames];

        if (! $this->settings->presetDraftOrder) {
            shuffle($playerNames);
        }

        foreach ($playerNames as $name) {
            $p = Player::create($name);
            $players[$p->id->value] = $p;
        }

        if ($this->settings->allianceMode) {
            $teamNames = $this->generateTeamNames();
            $teamPlayers = [];

            if ($this->settings->allianceTeamMode == AllianceTeamMode::RANDOM) {
                shuffle($players);
            }

            foreach(array_values($players) as $i => $player) {
                $teamName = $teamNames[(int) floor($i / 2)];
                $teamPlayers[$player->id->value] = $player->putInTeam($teamName);
            }

            $players = $teamPlayers;
        }

        return $players;
    }
}