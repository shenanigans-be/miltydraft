<?php

namespace App\Draft\Generators;

use App\Draft\Settings;
use App\TwilightImperium\Faction;

/**
 * Generates a pool of draftable factions based on settings
 */
class FactionPoolGenerator
{
    private readonly array $factionData;

    public function __construct(
        private readonly Settings $settings
    ) {
        $this->factionData = Faction::all();
    }

    /**
     * @return array<Faction>
     */
    public function generate(): array
    {
        $this->settings->seed->setForFactions();
        $factionsFromSets = $this->gatherFactionsFromSelectedSets();

        $gatheredFactions = [];

        if (!empty($this->settings->customFactions)) {
            foreach ($this->settings->customFactions as $f) {
                $gatheredFactions[] = $factionsFromSets[$f];
                // take out the selected faction, so it doesn't get re-drawn in the next part
                unset($factionsFromSets[$f]);
            }

            $factionsStillToGather = $this->settings->numberOfFactions - count($gatheredFactions);
            if ($factionsStillToGather > 0) {
                shuffle($factionsFromSets);
                $gatheredFactions = array_merge($gatheredFactions, array_slice($factionsFromSets, 0, $factionsStillToGather));
            }
        } else {
            $gatheredFactions = $factionsFromSets;
        }

        shuffle($gatheredFactions);
        return array_slice($gatheredFactions, 0, $this->settings->numberOfFactions);
    }

    private function gatherFactionsFromSelectedSets(): array
    {
        return array_filter(
            $this->factionData,
            fn (Faction $faction) =>
                in_array($faction->edition, $this->settings->factionSets) ||
                $faction->name == "The Council Keleres" && $this->settings->includeCouncilKeleresFaction
        );
    }
}