<?php

namespace App\Draft\Generators;

use App\Draft\Draft;
use App\Draft\DraftId;
use App\Draft\Player;
use App\Draft\PlayerId;
use App\Draft\Secrets;
use App\Draft\Settings;
use App\TwilightImperium\AllianceTeamMode;

class DraftGenerator
{
    private readonly FactionPoolGenerator $factionPoolGenerator;
    private readonly SlicePoolGenerator $slicePoolGenerator;

    public function __construct(
        private readonly Settings $settings
    )
    {
        $this->slicePoolGenerator = new SlicePoolGenerator($this->settings);
        $this->factionPoolGenerator = new FactionPoolGenerator($this->settings);
    }

    public function generate(): Draft
    {
        $players = $this->generatePlayerData();

        return new Draft(
            DraftId::generate(),
            false,
            $players,
            $this->settings,
            $this->generateSecrets(),
            $this->slicePoolGenerator->generate(),
            $this->factionPoolGenerator->generate(),
            [],
            PlayerId::fromString(array_key_first($players))
        );
    }

    protected function generateSecrets(): Secrets
    {
        return new Secrets(Secrets::generatePassword());
    }



    /**
     * @return array<string>
     */
    protected function generateTeamNames(): array
    {
        return array_slice(['A', 'B', 'C', 'D'], 0, count($this->playerNames) / 2);
    }

    /**
     * @return array<string, Player>
     */
    public function generatePlayerData(): array
    {
        $players = [];
        foreach ($this->settings->playerNames as $name) {
            $p = Player::create($name);
            $players[$p->id->value] = $p;
        }

        if ($this->settings->allianceMode) {
            $teamNames = $this->generateTeamNames();

            if ($this->settings->allianceTeamMode == AllianceTeamMode::RANDOM) {
                shuffle($players);
            }

            for ($i = 0; $i < count($players); $i+=2) {
                $teamName = $teamNames[$i/2];

                $players[] = $players[$i]->putInTeam($teamName);
                $players[] = $players[$i + 1]->putInTeam($teamName);
            }

        }

        if (!$this->settings->presetDraftOrder) {
            shuffle($players);
        }

        return $players;
    }

    /**
     * @return array<string, Player>
     */
    protected function generateTeamPlayerData(): array
    {
        $teams = $this->generateTeamNames();

        $players = [];
        foreach ($this->settings->playerNames as $name) {
            $p = Player::create($name);
            $players[$p->id->value] = $p;
        }
        return $players;
    }
}