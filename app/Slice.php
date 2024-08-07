<?php

namespace App;

class Slice
{
    public $tiles;
    public $tile_ids = [];
    public $total_influcence = 0;
    public $total_resources = 0;
    public $optimal_influence = 0;
    public $optimal_resources = 0;
    public $total_optimal = 0;
    public $wormholes = [];
    public $specialties = [];
    public $legendaries = [];

    /**
     * Slice constructor.
     * @param Tile[] $tiles
     */
    function __construct($tiles)
    {
        $this->tiles = $tiles;
        foreach ($tiles as $tile) {

            $this->tile_ids[] = $tile->id;
            $this->total_influcence += $tile->total_influence;
            $this->total_resources += $tile->total_resources;
            $this->optimal_influence += $tile->optimal_influence;
            $this->optimal_resources += $tile->optimal_resources;

            if ($tile->wormhole != null) $this->wormholes[] = $tile->wormhole;

            foreach ($tile->planets as $planet) {
                if ($planet->specialty != null) {
                    $this->specialties[] = $planet->specialty;
                }
                if ($planet->legendary) {
                    $this->legendaries[] = $planet->legendary;
                }
            }
        }

        $this->total_optimal = $this->optimal_resources + $this->optimal_influence;
    }

    public function toJson(): array
    {
        return [
            'tiles' => $this->tile_ids,
            'specialties' => $this->specialties,
            'wormholes' => $this->wormholes,
            'has_legendaries' => Tile::countSpecials($this->tiles)['legendary'] > 0,
            'legendaries' => $this->legendaries,
            'total_influence' => $this->total_influcence,
            'total_resources' => $this->total_resources,
            'optimal_influence' => $this->optimal_influence,
            'optimal_resources' => $this->optimal_resources
        ];
    }

    /**
     * @param GeneratorConfig $config
     * @return bool
     */
    function validate(GeneratorConfig $config): bool
    {
        $special_count = Tile::countSpecials($this->tiles);

        // can't have 2 alpha, beta or legendaries
        if ($special_count['alpha'] > 1 || $special_count['beta'] > 1 || $special_count['legendary'] > 1) {
            return false;
        }

        // has the right minimum optimal values?
        if ($this->optimal_influence < $config->minimum_optimal_influence || $this->optimal_resources < $config->minimum_optimal_resources) {
            //echo "not enough minimum<br />";
            return false;
        }

        if ($special_count['alpha'] > 0 && $special_count['beta'] > 0 && $config->max_1_wormhole) {
            return false;
        }

        // has the right total optimal value? (not too much, not too little)
        if ($this->total_optimal < $config->minimum_optimal_total || $this->total_optimal > $config->maximum_optimal_total) {
            return false;
        }

        return true;
    }

    function arrange($previous_tries = 0)
    {
        // miltydraft.com only shuffles it 12 times max, no idea why but we're gonna assume they have a reason
        if ($previous_tries > 12) {
            return false;
        }

        shuffle($this->tiles);
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
                return $this->arrange($previous_tries + 1);
            }
        }

        // if we're all good at this point then we should fix the order of the tile ids
        $this->tile_ids = [];
        foreach ($this->tiles as $t) {
            $this->tile_ids[] = $t->id;
        }

        return true;
    }

}
