<?php

namespace App\Draft\Generators;

use App\Draft\Exceptions\InvalidDraftSettingsException;
use App\Draft\Exceptions\InvalidSliceException;
use App\Draft\Settings;
use App\Draft\Slice;
use App\TwilightImperium\Tile;
use App\TwilightImperium\TileTier;
use App\TwilightImperium\Wormhole;

/**
 * Generates a pool of draftable slices based on settings
 */
class SlicePoolGenerator
{
    const MAX_TRIES = 4000;

    /**
     * @var array<string, Tile> $tileData
     */
    private readonly array $tileData;

    /** @var array<Tile> $allGatheredTiles  */
    private readonly array $allGatheredTiles;
    /** @var array<Tile> $gatheredHighTierTiles  */
    private array $gatheredHighTierTiles;
    /** @var array<Tile> $gatheredMediumTierTiles  */
    private array $gatheredMediumTierTiles;
    /** @var array<Tile> $gatheredLowTierTiles  */
    private array $gatheredLowTierTiles;
    /** @var array<Tile> $gatheredRedTiles  */
    private array $gatheredRedTiles;

    public int $tries;

    public function __construct(
        private readonly Settings $settings
    ) {
        $this->tileData = Tile::all();

        // make pre-selection based on tile sets
        $this->allGatheredTiles = array_filter(
            $this->tileData,
            fn (Tile $tile) =>
                in_array($tile->edition, $this->settings->tileSets) &&
                // tier none is mec rex and such...
                $tile->tier != TileTier::NONE
        );

        // sort pre-selected tiles in tiers
        $highTier = [];
        $midTier = [];
        $lowTier = [];
        $redTier = [];

        foreach($this->allGatheredTiles as $tile) {
            switch($tile->tier) {
                case TileTier::HIGH:
                    $highTier[] = $tile;
                    break;
                case TileTier::MEDIUM:
                    $midTier[] = $tile;
                    break;
                case TileTier::LOW:
                    $lowTier[] = $tile;
                    break;
                case TileTier::RED:
                    $redTier[] = $tile;
                    break;
            };
        }

        $this->gatheredHighTierTiles = $highTier;
        $this->gatheredMediumTierTiles = $midTier;
        $this->gatheredLowTierTiles = $lowTier;
        $this->gatheredRedTiles = $redTier;
    }

    /**
     * @return array<Slice>
     */
    public function generate(): array
    {
        if (!empty($this->settings->customSlices)) {
            return $this->slicesFromCustomSlices();
        } else {
            return $this->attemptToGenerate();
        }
    }


    private function attemptToGenerate(int $previousTries = 0): array
    {
        if ($previousTries > self::MAX_TRIES) {
            throw InvalidDraftSettingsException::cannotGenerateSlices();
        }

        $this->settings->seed->setForSlices($previousTries);

        shuffle($this->gatheredHighTierTiles);
        shuffle($this->gatheredMediumTierTiles);
        shuffle($this->gatheredLowTierTiles);
        shuffle($this->gatheredRedTiles);

        // we need one high, medium, low and 2 red tier tiles per slice
        $highTier = array_slice($this->gatheredHighTierTiles, 0, $this->settings->numberOfSlices);
        $midTier = array_slice($this->gatheredMediumTierTiles, 0, $this->settings->numberOfSlices);
        $lowTier = array_slice($this->gatheredLowTierTiles, 0, $this->settings->numberOfSlices);
        $redTier = array_slice($this->gatheredRedTiles, 0, $this->settings->numberOfSlices * 2);

        $validSelection = $this->validateTileSelection(array_merge(
            $highTier,
            $midTier,
            $lowTier,
            $redTier
        ));

        if (!$validSelection) {
            return $this->attemptToGenerate($previousTries + 1);
        }

        $slices = [];
        for ($i = 0; $i < $this->settings->numberOfSlices; $i++) {
            $slice = new Slice([
                $highTier[$i],
                $midTier[$i],
                $lowTier[$i],
                $redTier[$i * 2],
                $redTier[($i * 2) + 1]
            ]);
            try {
                $slice->validate(
                    $this->settings->minimumOptimalInfluence,
                    $this->settings->minimumOptimalResources,
                    $this->settings->minimumOptimalTotal,
                    $this->settings->maximumOptimalTotal,
                    $this->settings->maxOneWormholesPerSlice ? 1 : null
                );
                $slice->arrange($this->settings->seed);

                // if we didn't run into any exceptions here: it's a good slice!
                $slices[] = $slice;
            } catch (InvalidSliceException $invalidSlice) {
                return $this->attemptToGenerate($previousTries + 1);
            }
        }

        $this->tries = $previousTries;
        return $slices;
    }

    /**
     * @param array<Tile> $tiles
     * @return bool
     */
    private function validateTileSelection(array $tiles): bool
    {
        $alphaWormholeCount = 0;
        $betaWormholeCount = 0;
        $legendaryPlanetCount = 0;

        foreach($tiles as $t) {
            if ($t->hasWormhole(Wormhole::ALPHA)) {
                $alphaWormholeCount++;
            }
            if ($t->hasWormhole(Wormhole::BETA)) {
                $betaWormholeCount++;
            }
            if ($t->hasLegendaryPlanet()) {
                $legendaryPlanetCount++;
            }
        }

        if ($legendaryPlanetCount < $this->settings->minimumLegendaryPlanets) {
            return false;
        }

        if (
           $this->settings->minimumTwoAlphaAndBetaWormholes &&
           ($alphaWormholeCount < 2 || $betaWormholeCount < 2)
        ) {
            return false;
        }

        return true;
    }

    /**
     * @return array<Slice>
     */
    private function slicesFromCustomSlices(): array
    {
        return array_map(function (array $sliceData) {
            $tileData = array_map(fn ($tileId) => $this->tileData[$tileId], $sliceData);
            return new Slice($tileData);
        }, $this->settings->customSlices);
    }

    /**
     * @param array<Tile> $tiles
     * @return array<string>
     */
    private function pluckTileIds(array $tiles): array
    {
        return array_map(fn(Tile $t) => $t->id, $tiles);
    }

    /**
     * Debug and test methods
     */

    public function gatheredTilesIds(): array
    {
        return $this->pluckTileIds($this->allGatheredTiles);
    }

    public function gatheredTiles(): array
    {
        return $this->allGatheredTiles;
    }

    public function gatheredTileTierIds(): array
    {
        return array_map(fn (array $tier) => $this->pluckTileIds($tier), $this->gatheredTileTiers());
    }

    public function gatheredTileTiers(): array
    {
        return [
            TileTier::HIGH->value => $this->gatheredHighTierTiles,
            TileTier::MEDIUM->value => $this->gatheredMediumTierTiles,
            TileTier::LOW->value => $this->gatheredLowTierTiles,
            TileTier::RED->value => $this->gatheredRedTiles,
        ];
    }
}