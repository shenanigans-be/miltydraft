<?php

namespace App\Draft;

use App\TwilightImperium\AllianceTeamMode;
use App\TwilightImperium\AllianceTeamPosition;
use App\TwilightImperium\Edition;

class DraftSettings
{
    public function __construct(
        /**
         * @var array<string> $players
         */
        public array $players,
        public bool $presetDraftOrder,
        public DraftName $name,
        public DraftSeed $seed,
        public int $numberOfSlices,
        public int $numberOfFactions,
        /**
         * @var array<Edition>
         */
        public array $tileSets,
        /**
         * @var array<Edition>
         */
        public array $factionSets,
        // @todo figure out a better way to integrate this
        // should be required-ish when TE is included, but optional if PoK is included
        public bool $includeCouncilKeleresFaction,
        public int $minimumWormholes,
        public bool $maxOneWormholesPerSlice,
        public int $minimumLegendaryPlanets,
        public float $minimumOptimalInfluence,
        public float $minimumOptimalResources,
        public float $minimumOptimalTotal,
        public float $maximumOptimalTotal,
        public array $customFactions,
        public array $customSlices,
        public bool $allianceMode,
        public ?AllianceTeamMode $allianceTeamMode = null,
        public ?AllianceTeamPosition $allianceTeamPosition = null,
        public ?bool $allianceForceDoublePicks = null
    ) {
    }

    protected function includesFactionSet(Edition $e): bool {
        return in_array($e, $this->factionSets);
    }

    protected function includesTileSet(Edition $e): bool {
        return in_array($e, $this->tileSets);
    }

    public function toArray()
    {
        return [
            'players' => $this->players,
            'num_players' => count($this->players),
            'preset_draft_order' => $this->presetDraftOrder,
            'name' => (string) $this->name,
            'num_slices' => $this->numberOfSlices,
            'num_factions' => $this->numberOfFactions,
            // tiles
            'include_pok' => $this->includesTileSet(Edition::PROPHECY_OF_KINGS),
            'include_ds_tiles' => $this->includesTileSet(Edition::DISCORDANT_STARS_PLUS),
            'include_te_tiles' => $this->includesTileSet(Edition::THUNDERS_EDGE),
            // @todo refactor frontend to use this. Don't break backwards compatibility!
            'tile_sets' => $this->tileSets,
            // factions
            'include_base_factions' => $this->includesFactionSet(Edition::BASE_GAME),
            'include_pok_factions' => $this->includesFactionSet(Edition::PROPHECY_OF_KINGS),
            'include_te_factions' => $this->includesFactionSet(Edition::THUNDERS_EDGE),
            'include_discordant' => $this->includesFactionSet(Edition::DISCORDANT_STARS),
            'include_discordantexp' => $this->includesFactionSet(Edition::DISCORDANT_STARS_PLUS),
            'include_keleres' => $this->includeCouncilKeleresFaction,
            // slice settings
            'min_wormholes' => $this->minimumWormholes,
            'max_1_wormhole' => $this->maxOneWormholesPerSlice,
            'min_legendaries' => $this->minimumLegendaryPlanets,
            // @todo refactor frontend to use this instead of min_legendaries. Don't break backwards compatibility!
            'minimum_legendary_planets' => $this->minimumLegendaryPlanets,
            'minimum_optimal_influence' => $this->minimumOptimalInfluence,
            'minimum_optimal_resources' => $this->minimumOptimalResources,
            'minimum_optimal_total' => $this->minimumOptimalTotal,
            'maximum_optimal_total' => $this->maximumOptimalTotal,
            'custom_factions' => $this->customFactions,
            'custom_slices' => $this->customSlices,
            'seed' => $this->seed->getValue(),
            'alliance' => $this->allianceMode ? [
                'alliance_teams' => $this->allianceTeamMode->value,
                'alliance_teams_position' => $this->allianceTeamPosition->value,
                'force_double_picks' => $this->allianceForceDoublePicks
            ] : null
        ];
    }

    // @todo validate
}