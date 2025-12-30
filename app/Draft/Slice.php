<?php

declare(strict_types=1);

namespace App\Draft;

use App\TwilightImperium\TechSpecialties;
use App\TwilightImperium\Tile;
use App\TwilightImperium\Wormhole;

class Slice
{
    protected const MAX_ARRANGEMENT_TRIES = 100;

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
        public array $tiles,
    ) {
        // if the slice doesn't have 5 tiles in it, something went awry
        if (count($this->tiles) != 5) {
            throw new \Exception('Slice does not have enough tiles');
        }

        foreach ($tiles as $tile) {
            $this->totalInfluence += $tile->totalInfluence;
            $this->totalResources += $tile->totalResources;
            $this->optimalInfluence += $tile->optimalInfluence;
            $this->optimalResources += $tile->optimalResources;
            $this->optimalTotal += $tile->optimalTotal;

            $this->wormholes = array_merge($this->wormholes, $tile->wormholes);

            foreach ($tile->planets as $planet) {
                foreach ($planet->specialties as $spec) {
                    $this->specialties[] = $spec;
                }
                if ($planet->legendary) {
                    $this->legendaryPlanets[] = $planet->name . ': ' . $planet->legendary;
                }
            }
        }
    }

    public function toJson(): array
    {
        // @refactor so that tile ids just get imported from json and then populated from those tiles
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
            'optimal_resources' => $this->optimalResources,
        ];
    }

    /**
     * @todo don't use countSpecials
     */
    public function validate(
        float $minimumOptimalInfluence,
        float $minimumOptimalResources,
        float $minimumOptimalTotal,
        float $maximumOptimalTotal,
        bool $maxOneWormhole,
    ): bool {
        $specialCount = Tile::countSpecials($this->tiles);

        // can't have 2 alpha, beta or legendary planets
        if ($specialCount['alpha'] > 1 || $specialCount['beta'] > 1 || $specialCount['legendary'] > 1) {
            return false;
        }

        // has the right minimum optimal values?
        if (
            $this->optimalInfluence < $minimumOptimalInfluence ||
            $this->optimalResources < $minimumOptimalResources
        ) {
            return false;
        }

        if ($maxOneWormhole && $specialCount['alpha'] + $specialCount['beta'] > 1) {
            return false;
        }

        // has the right total optimal value? (not too much, not too little)
        if (
            $this->optimalTotal < $minimumOptimalTotal ||
            $this->optimalTotal > $maximumOptimalTotal
        ) {
            return false;
        }

        return true;
    }

    public function arrange(Seed $seed): bool {
        $tries = 0;
        // always shuffle at least once
        $seed->setForSlices($tries);
        shuffle($this->tiles);

        while (! $this->tileArrangementIsValid()) {
            $tries++;
            $seed->setForSlices($tries);
            shuffle($this->tiles);

            if ($tries > self::MAX_ARRANGEMENT_TRIES) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the tiles (in the current order) are a valid arrangement
     *
     * tiles are laid out like this:
     *       4
     *   3
     *       1
     *   0       2
     *       H
     * so for example, tile #1 neighbours #0, #3 and #4. #2 only neighbours #1
     * And we want to avoid two neighbouring anomalies (That's in the rules).
     *
     * The nature of milty draft makes it so that you can't predict the placement of slices,
     * so neighbouring anomalies might happen just by virtue of player order and slice choice.
     * But since we can't really do anything about that, we just stop enforce the rule here.
     *
     * @return bool
     */
    public function tileArrangementIsValid(): bool
    {

        $neighbours = [[0, 1], [0, 3], [1, 2], [1, 3], [1, 4], [3, 4]];

        foreach ($neighbours as $neighbouringPair) {
            // can't have two neighbouring anomalies
            if (
                $this->tiles[$neighbouringPair[0]]->hasAnomaly() &&
                $this->tiles[$neighbouringPair[1]]->hasAnomaly()
            ) {
                return false;
            }
        }

        return true;
    }

    public function tileIds(): array
    {
        return array_map(fn (Tile $t) => $t->id, $this->tiles);
    }

    public function hasLegendary(): bool
    {
        return count($this->legendaryPlanets) > 0;
    }

    public function hasWormhole(Wormhole $wormhole): bool
    {
        return in_array($wormhole, $this->wormholes);
    }
}
