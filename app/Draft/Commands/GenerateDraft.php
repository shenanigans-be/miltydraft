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

        // Preset teams are defined by the order players are entered in the form
        // (adjacent pairs form a team). Capture that mapping from the original
        // order *before* the draft-order shuffle below can scramble it.
        $presetTeams = [];
        if ($this->settings->allianceMode && $this->settings->allianceTeamMode == AllianceTeamMode::PRESET) {
            $teamNames = $this->generateTeamNames();
            foreach (array_values($playerNames) as $i => $name) {
                $presetTeams[$name] = $teamNames[(int) floor($i / 2)];
            }
        }

        if (! $this->settings->presetDraftOrder) {
            shuffle($playerNames);
        }

        foreach ($playerNames as $name) {
            $p = Player::create($name);
            $players[$p->id->value] = $p;
        }

        if ($this->settings->allianceMode) {
            $teamPlayers = [];

            if ($this->settings->allianceTeamMode == AllianceTeamMode::PRESET) {
                // Teams are fixed: keep each player's preset team regardless of
                // the (possibly randomised) draft order.
                foreach ($players as $id => $player) {
                    $teamPlayers[$id] = $player->putInTeam($presetTeams[$player->name]);
                }
            } else {
                // Random teams: pair players up by a fresh shuffle.
                $teamNames = $this->generateTeamNames();
                $shuffled = array_values($players);
                shuffle($shuffled);

                foreach ($shuffled as $i => $player) {
                    $teamName = $teamNames[(int) floor($i / 2)];
                    $teamPlayers[$player->id->value] = $player->putInTeam($teamName);
                }
            }

            $players = $teamPlayers;
        }

        return $players;
    }
}