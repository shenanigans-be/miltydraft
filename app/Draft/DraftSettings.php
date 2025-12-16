<?php

namespace App\Draft;

use App\TwilightImperium\AllianceTeamMode;
use App\TwilightImperium\AllianceTeamPosition;
use App\TwilightImperium\Edition;

/**
 * @todo This class is friggin huge. We could sepatate all the validators into their own class
 * or have SliceSettings, FactionSettings,...
 */
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

    public function includesFactionSet(Edition $e): bool {
        return in_array($e, $this->factionSets);
    }

    public function includesTileSet(Edition $e): bool {
        return in_array($e, $this->tileSets);
    }

    public function toArray()
    {
        return [
            'players' => $this->players,
            'preset_draft_order' => $this->presetDraftOrder,
            'name' => (string) $this->name,
            'num_slices' => $this->numberOfSlices,
            'num_factions' => $this->numberOfFactions,
            // tiles
            'include_pok' => $this->includesTileSet(Edition::PROPHECY_OF_KINGS),
            'include_ds_tiles' => $this->includesTileSet(Edition::DISCORDANT_STARS_PLUS),
            'include_te_tiles' => $this->includesTileSet(Edition::THUNDERS_EDGE),
            // @todo refactor frontend to use tile sets. Backwards compatibility!
            // factions
            'include_base_factions' => $this->includesFactionSet(Edition::BASE_GAME),
            'include_pok_factions' => $this->includesFactionSet(Edition::PROPHECY_OF_KINGS),
            'include_te_factions' => $this->includesFactionSet(Edition::THUNDERS_EDGE),
            'include_discordant' => $this->includesFactionSet(Edition::DISCORDANT_STARS),
            'include_discordantexp' => $this->includesFactionSet(Edition::DISCORDANT_STARS_PLUS),
            'include_keleres' => $this->includeCouncilKeleresFaction,
            // @todo refactor frontend to use faction sets. Backwards compatibility!
            // slice settings
            'min_wormholes' => $this->minimumWormholes,
            'max_1_wormhole' => $this->maxOneWormholesPerSlice,
            // @todo refactor frontend to use this instead of min_legendary_planets. Don't break backwards compatibility!
            'min_legendaries' => $this->minimumLegendaryPlanets,
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

    /**
     * @return bool
     * @throws InvalidDraftSettingsException
     */
    public function validate(): bool
    {
        /*

        if ($this->custom_slices != null) {
            if (count($this->custom_slices) < count($this->players)) return_error("Not enough custom slices for number of players");
            foreach ($this->custom_slices as $s) {
                if (count($s) != 5) return_error('Some of the custom slices have the wrong number of tiles. (each should have five)');
            }
        }
        */
        if (!$this->seed->isValid()) {
            throw InvalidDraftSettingsException::invalidSeed();
        }


        $this->validatePlayers();
        $this->validateTiles();
        $this->validateFactions();
        $this->validateCustomSlices();

        return true;
    }

    protected function validatePlayers(): bool
    {
        if (count(array_unique($this->players)) != count($this->players)) {
            throw InvalidDraftSettingsException::playerNamesNotUnique();
        }

        if (count($this->players) < 3) {
            throw InvalidDraftSettingsException::notEnoughPlayers();
        }

        if (count($this->players) > $this->numberOfSlices) {
            throw InvalidDraftSettingsException::notEnoughSlicesForPlayers();
        }

        if (count($this->players) > $this->numberOfFactions) {
            throw InvalidDraftSettingsException::notEnoughFactionsForPlayers();
        }

        return true;
    }

    protected function validateTiles() {
        // @todo base this on tile-selection.json instead of constants
        // better yet: make a tileset php class that contain the data instead of loading json

        $blueTiles = array_reduce($this->tileSets, fn ($sum, Edition $e) => $sum += $e->blueTileCount(), 0);
        $redTiles = array_reduce($this->tileSets, fn ($sum, Edition $e) => $sum += $e->redTileCount(), 0);
        $legendaryPlanets = array_reduce($this->tileSets, fn ($sum, Edition $e) => $sum += $e->legendaryPlanetCount(), 0);

        $maxSlices = min(floor($blueTiles/3), floor($redTiles/2));

        if ($this->numberOfSlices > $maxSlices) {
            throw InvalidDraftSettingsException::notEnoughTilesForSlices($maxSlices);
        }

        if ($this->maximumOptimalTotal < $this->minimumOptimalTotal) {
            throw InvalidDraftSettingsException::invalidMaximumOptimal();
        }

        if ($this->minimumLegendaryPlanets > $this->numberOfSlices) {
            throw InvalidDraftSettingsException::notEnoughSlicesForLegendaryPlanets();
        }

        if ($this->minimumLegendaryPlanets > $legendaryPlanets) {
            throw InvalidDraftSettingsException::notEnoughLegendaryPlanets($legendaryPlanets);
        }
    }

    protected function validateFactions()
    {
        $factions = array_reduce($this->factionSets, fn ($sum, Edition $e) => $sum += $e->factionCount(), 0);
        if ($factions < $this->numberOfFactions) {
            throw InvalidDraftSettingsException::notEnoughFactionsInSet($factions);
        }
    }

    protected function validateCustomSlices(): bool
    {
        if (!empty($this->customSlices)) {
            if (count($this->customSlices) < count($this->players)) {
                throw InvalidDraftSettingsException::notEnoughCustomSlices();
            }
            foreach ($this->customSlices as $s) {
                if (count($s) != 5) {
                    throw InvalidDraftSettingsException::invalidCustomSlices();
                }
            }
        }

        return true;
    }

    public static function fromJson(array $data): self
    {
        $allianceMode = $data['alliance'] != null;

        return new self(
            $data['players'],
            $data['preset_draft_order'],
            new DraftName($data['name']),
            new DraftSeed($data['seed']),
            $data['num_slices'],
            $data['num_factions'],
            self::tileSetsFromJson($data),
            self::factionSetsFromJson($data),
            $data['include_keleres'],
            $data['min_wormholes'],
            $data['max_1_wormhole'],
            $data['min_legendaries'],
            (float) $data['minimum_optimal_influence'],
            (float) $data['minimum_optimal_resources'],
            (float) $data['minimum_optimal_total'],
            (float) $data['maximum_optimal_total'],
            $data['custom_factions'] ?? [],
            $data['custom_slices'] ?? [],
            $allianceMode,
            $allianceMode ? AllianceTeamMode::from($data['alliance']['alliance_teams']) : null,
            $allianceMode ? AllianceTeamPosition::from($data['alliance']['alliance_teams_position']) : null,
            $allianceMode ? (bool) $data['alliance']['force_double_picks'] : null,
        );
    }

    /**
     * @param $data
     * @return array<Edition>
     */
    private static function tileSetsFromJson($data): array
    {
        $tilesets = [];

        // currently there's no way to disable base game tiles
        $tilesets[] = Edition::BASE_GAME;
        if ($data['include_pok']) {
            $tilesets[] = Edition::PROPHECY_OF_KINGS;
        }
        if ($data['include_ds_tiles']) {
            $tilesets[] = Edition::DISCORDANT_STARS_PLUS;
        }
        if ($data['include_te_tiles']) {
            $tilesets[] = Edition::THUNDERS_EDGE;
        }

        return $tilesets;
    }

    /**
     * @param $data
     * @return array<Edition>
     */
    private static function factionSetsFromJson($data): array
    {
        $tilesets = [];

        if ($data['include_base_factions']) {
            $tilesets[] = Edition::BASE_GAME;
        }
        if ($data['include_pok_factions']) {
            $tilesets[] = Edition::PROPHECY_OF_KINGS;
        }
        if ($data['include_discordant']) {
            $tilesets[] = Edition::DISCORDANT_STARS;
        }
        if ($data['include_discordantexp']) {
            $tilesets[] = Edition::DISCORDANT_STARS_PLUS;
        }
        if ($data['include_te_factions']) {
            $tilesets[] = Edition::THUNDERS_EDGE;
        }

        return $tilesets;
    }
}