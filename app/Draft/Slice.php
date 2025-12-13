<?php

namespace App\Draft;

use App\TwilightImperium\TechSpecialties;
use App\TwilightImperium\Tile;
use App\TwilightImperium\Wormhole;

class Slice
{
    protected const MAX_ARRANGEMENT_TRIES = 12;

    protected array $tileArrangement;
    /**
     * @var array<Wormhole>
     */
    public array $wormholes = [];
    /**
     * @var array<TechSpecialties>
     */
    public array $specialties = [];
    /**
     * @var array<string>
     */
    public array $legendaryPlanets = [];
    public int $totalInfluence = 0;
    public int $totalResources = 0;
    public float $optimalResources = 0;
    public float $optimalInfluence = 0;
    public float $optimalTotal = 0;

    /**
     * @param Tile[] $tiles
     */
    function __construct(
        public array $tiles
    ) {
        // if the slice doesn't have 5 tiles in it, something went awry
        if (count($this->tiles) != 5) {
            throw InvalidSliceException::notEnoughTiles();
        }

        foreach ($tiles as $tile) {
            $this->totalInfluence += $tile->totalInfluence;
            $this->totalResources += $tile->totalResources;
            $this->optimalInfluence += $tile->optimalInfluence;
            $this->optimalResources += $tile->optimalResources;
            $this->optimalTotal += $tile->optimalTotal;

            $this->wormholes = array_merge($tile->wormholes);

            foreach ($tile->planets as $planet) {
                foreach ($planet->specialties as $spec) {
                    $this->specialties[] = $spec;
                }
                if ($planet->legendary) {
                    $this->legendaryPlanets[] = $planet->legendary;
                }
            }
        }
    }

    public function toJson(): array
    {
        return [
            'tiles' => array_map(fn(Tile $tile) => $tile->id, $this->tiles),
            'specialties' => $this->specialties,
            'wormholes' => $this->wormholes,
            // @todo: refactor to has_legendary_planets, but don't break backwards compatibility!
            // or maybe just get rid altogether, since you can just check legendaries/legendary_planets
            'has_legendaries' => Tile::countSpecials($this->tiles)['legendary'] > 0,
            // @todo: refactor to legendary_planets, but don't break backwards compatibility!
            'legendaries' => $this->legendaryPlanets,
            'total_influence' => $this->totalInfluence,
            'total_resources' => $this->totalResources,
            'optimal_influence' => $this->optimalInfluence,
            'optimal_resources' => $this->optimalResources
        ];
    }

    /**
     * @throws InvalidSliceException
     */
    public function validate(
        float $minimumOptimalInfluence,
        float $minimumOptimalResources,
        float $minimumOptimalTotal,
        float $maximumOptimalTotal,
        ?int $maxWormholes = null
    ): bool {
        $specialCount = Tile::countSpecials($this->tiles);

        // can't have 2 alpha, beta or legendary planets
        if ($specialCount['alpha'] > 1 || $specialCount['beta'] > 1 || $specialCount['legendary'] > 1) {
            throw InvalidSliceException::doesNotMeetRequirements("Too many wormholes or legendary planets");
        }

        // has the right minimum optimal values?
        if (
            $this->optimalInfluence < $minimumOptimalInfluence ||
            $this->optimalResources < $minimumOptimalResources
        ) {
            throw InvalidSliceException::doesNotMeetRequirements("Minimal influence/resources too low");
        }

        if ($maxWormholes != null && $specialCount['alpha'] + $specialCount['beta'] > $maxWormholes) {
            throw InvalidSliceException::doesNotMeetRequirements("More than allowed number of wormholes");
        }

        // has the right total optimal value? (not too much, not too little)
        if (
            $this->optimalTotal < $minimumOptimalTotal ||
            $this->optimalTotal > $maximumOptimalTotal
        ) {
            throw InvalidSliceException::doesNotMeetRequirements("Optimal values too high or low");
        }

        return true;
    }

    public function arrange(): void {
        $tries = 0;
        while (!$this->tileArrangementIsValid()) {
            shuffle($this->tiles);
            $tries++;

            if ($tries > self::MAX_ARRANGEMENT_TRIES) {
                throw InvalidSliceException::hasNoValidArragenemnt();
            }
        }
    }

    public function tileArrangementIsValid(): bool
    {
        // tiles are laid out like this:
        //      4
        //  3
        //      1
        //  0       2
        //      H
        // so for example, tile #1 neighbours #0, #3 and #4. #2 only neighbours #1

        $neighbours = [[0, 1], [0, 3], [1, 2], [1, 3], [1, 4], [3, 4]];

        foreach ($neighbours as $edge) {
            // can't have two neighbouring anomalies
            if ($this->tiles[$edge[0]]->hasAnomaly() && $this->tiles[$edge[1]]->hasAnomaly()) {
                return false;
            }
        }

        return true;
    }
}
