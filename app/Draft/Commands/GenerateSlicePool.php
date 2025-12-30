<?php

declare(strict_types=1);

namespace App\Draft\Commands;

use App\Draft\Exceptions\InvalidDraftSettingsException;
use App\Draft\Settings;
use App\Draft\Slice;
use App\Draft\TilePool;
use App\Shared\Command;
use App\TwilightImperium\Tile;
use App\TwilightImperium\TileTier;
use App\TwilightImperium\Wormhole;

class GenerateSlicePool implements Command
{
    const MAX_TILE_SELECTION_TRIES = 100;
    const MAX_SLICES_FROM_SELECTION_TRIES = 400;

    /**
     * @var array<string, Tile>
     */
    private readonly array $tileData;

    /** @var array<Tile> */
    private readonly array $allGatheredTiles;
    private readonly TilePool $gatheredTiles;

    public int $tries;

    public function __construct(
        private readonly Settings $settings,
    ) {
        $this->tileData = Tile::all();

        // make pre-selection based on tile sets
        $this->allGatheredTiles = array_filter(
            $this->tileData,
            fn (Tile $tile) =>
                in_array($tile->edition, $this->settings->tileSets) &&
                // tier none is mec rex and such...
                $tile->tier != TileTier::NONE,
        );

        // sort pre-selected tiles in tiers
        $highTier = [];
        $midTier = [];
        $lowTier = [];
        $redTier = [];

        foreach($this->allGatheredTiles as $tile) {
            switch($tile->tier) {
                case TileTier::HIGH:
                    $highTier[] = $tile->id;

                    break;
                case TileTier::MEDIUM:
                    $midTier[] = $tile->id;

                    break;
                case TileTier::LOW:
                    $lowTier[] = $tile->id;

                    break;
                case TileTier::RED:
                    $redTier[] = $tile->id;

                    break;
            };
        }

        $this->gatheredTiles = new TilePool(
            $highTier,
            $midTier,
            $lowTier,
            $redTier,
        );
    }

    /** @return array<Slice> */
    public function handle(): array
    {
        if (! empty($this->settings->customSlices)) {
            return $this->slicesFromCustomSlices();
        } else {
            return $this->attemptToGenerate();
        }
    }

    private function attemptToGenerate($previousTries = 0): array
    {
        $slices = [];

        if ($previousTries > self::MAX_TILE_SELECTION_TRIES) {
            throw InvalidDraftSettingsException::cannotGenerateSlices();
        }

        $this->settings->seed->setForSlices($previousTries);
        $this->gatheredTiles->shuffle();
        $tilePool = $this->gatheredTiles->slice($this->settings->numberOfSlices);

        $tilePoolIsValid = $this->validateTileSelection($tilePool->allIds());

        if (! $tilePoolIsValid) {
            return $this->attemptToGenerate($previousTries + 1);
        }

        $validSlicesFromPool = $this->makeSlicesFromPool($tilePool);
        if (empty($validSlicesFromPool)) {
            unset($validSlicesFromPool);

            return $this->attemptToGenerate($previousTries + 1);
        } else {
            return $validSlicesFromPool;
        }
    }

    private function makeSlicesFromPool(TilePool $pool, $previousTries = 0): array
    {
        if ($previousTries > self::MAX_SLICES_FROM_SELECTION_TRIES) {
            return [];
        }

        $this->settings->seed->setForSlices($previousTries);
        $pool->shuffle();

        $slices = [];

        for ($i = 0; $i < $this->settings->numberOfSlices; $i++) {
            $slice = new Slice([
                $this->tileData[$pool->highTier[$i]],
                $this->tileData[$pool->midTier[$i]],
                $this->tileData[$pool->lowTier[$i]],
                $this->tileData[$pool->redTier[$i * 2]],
                $this->tileData[$pool->redTier[($i * 2) + 1]],
            ]);

            $sliceIsValid = $slice->validate(
                $this->settings->minimumOptimalInfluence,
                $this->settings->minimumOptimalResources,
                $this->settings->minimumOptimalTotal,
                $this->settings->maximumOptimalTotal,
                $this->settings->maxOneWormholesPerSlice,
            );

            if (! $sliceIsValid) {
                unset($slice);
                unset($slices);

                return $this->makeSlicesFromPool($pool, $previousTries + 1);
            }

            if(! $slice->arrange($this->settings->seed)) {
                unset($slice);
                unset($slices);

                return $this->makeSlicesFromPool($pool, $previousTries);
            }

            $slices[] = $slice;
        }

        return $slices;
    }

    /**
     * @param array $tileIds
     * @return bool
     */
    private function validateTileSelection(array $tileIds): bool
    {
        $tileInfo = array_map(fn (string $id) => $this->tileData[$id], $tileIds);

        $alphaWormholeCount = 0;
        $betaWormholeCount = 0;
        $legendaryPlanetCount = 0;

        foreach($tileInfo as $t) {
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

            $tileData = [];
            foreach ($sliceData as $tileId) {
                if (!isset($this->tileData[$tileId])) {
                    throw InvalidDraftSettingsException::unknownTileInCustomSlice($tileId);
                }
                $tileData[] = $this->tileData[$tileId];
            }

            return new Slice($tileData);
        }, $this->settings->customSlices);
    }

    /**
     * Debug and test methods
     */
    public function gatheredTiles(): array
    {
        return $this->allGatheredTiles;
    }

    public function gatheredTileTiers(): TilePool
    {
        return $this->gatheredTiles;
    }
}